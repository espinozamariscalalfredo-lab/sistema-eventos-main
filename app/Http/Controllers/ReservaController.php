<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Atiende el calendario, las reservas, la papelera y el protocolo.
// Cambio documentado: 13/10/2026. Aquí se aplican las reglas antes de guardar.
class ReservaController extends Controller
{
    // 19/10/2026: el prefijo queda en reservas.titulo, sin crear columnas nuevas.
    private function leerEstadoTitulo(string $titulo): array
    {
        $marcador = '/^\s*\[CANCELADO\]\s*/iu';
        $cancelado = preg_match($marcador, $titulo) === 1;

        return [
            preg_replace($marcador, '', $titulo, 1) ?? $titulo,
            $cancelado ? 'cancelado' : 'programado',
        ];
    }

    private function guardarEstadoEnTitulo(string $titulo, string $estado): string
    {
        [$tituloLimpio] = $this->leerEstadoTitulo($titulo);

        return $estado === 'cancelado' ? '[CANCELADO] '.$tituloLimpio : $tituloLimpio;
    }

    private function salonesValidos(?Reserva $reserva = null): array
    {
        // Conserva el salón actual aunque ya esté inactivo.
        $nombres = Salon::where('activo', true)->pluck('nombre')->all();
        if ($reserva && ! in_array($reserva->salon, $nombres, true)) {
            $nombres[] = $reserva->salon;
        }

        return $nombres;
    }

    // Devuelve quién hizo el último cambio.
    private function actorActual(): string
    {
        $usuario = auth()->user();

        return $usuario->correo ?? $usuario->nombre ?? 'Administrador';
    }

    // Convierte reservas al formato de FullCalendar.
    public function obtenerReservas(Request $request)
    {
        // Usa los colores guardados en el catálogo.
        $salonColors = Salon::pluck('color', 'nombre')->all();

        $rol = auth()->user()?->rol;
        $reservas = Reserva::all()->map(function ($reserva) use ($salonColors, $rol) {
            // Las reservas antiguas sin salón usan un color de respaldo.
            $color = $salonColors[$reserva->salon] ?? '#f59e0b';
            [$titulo, $estado] = $this->leerEstadoTitulo($reserva->titulo);

            $evento = [
                // FullCalendar requiere estas tres claves en inglés.
                'id' => $reserva->id,
                'title' => $titulo,
                'start' => $reserva->fecha_inicio,
                'end' => $reserva->fecha_fin,
                'display' => 'block',
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'salon' => $reserva->salon,
                'estado' => $estado,
            ];

            if ($rol === 'superadmin') {
                $evento += [
                    'nombre_usuario' => $reserva->nombre_usuario,
                    'telefono_usuario' => $reserva->telefono_usuario,
                    'nombre_autoridad' => $reserva->nombre_autoridad,
                    'responsable' => $reserva->responsable,
                    'tipo_evento' => $reserva->tipo_evento,
                    'aforo' => $reserva->aforo,
                    'tipo_ingreso' => $reserva->tipo_ingreso,
                    'requerimientos' => $reserva->requerimientos,
                    'requerimientos_especiales' => $reserva->requerimientos_especiales,
                    'registrado_por' => $reserva->registrado_por,
                    'coordinador_interno' => $reserva->coordinador_interno,
                    'modificado_por' => $reserva->modificado_por,
                    'fecha_creacion' => $reserva->fecha_creacion,
                ];
            }

            return $evento;
        });

        return response()->json($reservas);
    }

    
    // Lista reservas y combina los filtros disponibles.
    public function supervisar(Request $request)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede supervisar las reservas.');

        $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        // Catálogo de salones.
        $salones = Salon::orderBy('orden')
            ->orderBy('nombre')
            ->get();
        $coloresSalones = $salones->pluck('color', 'nombre')->all();

        // Valores seleccionados en los filtros.
        $salonSeleccionado = $request->query('salon');
        $responsableSeleccionado = $request->query('responsable');
        $autoridadSeleccionada = $request->query('autoridad');
        $fechaSeleccionada = $request->query('fecha');
        $buscar = trim((string) $request->query('buscar', ''));

        $aplicarBusqueda = function ($consulta) use ($buscar) {
            if ($buscar === '') {
                return;
            }

            $coincidencia = '%'.$buscar.'%';
            $digitosFecha = preg_replace('/\D+/', '', $buscar);
            $buscarFechaNumerica = preg_match('/^[\d\s\/.-]+$/u', $buscar) === 1
                && strlen($digitosFecha) >= 4;

            $consulta->where(function ($query) use ($coincidencia, $digitosFecha, $buscarFechaNumerica) {
                $query->where('salon', 'ILIKE', $coincidencia)
                    ->orWhereRaw("to_char(fecha_inicio, 'DD-MM-YYYY HH24:MI') ILIKE ?", [$coincidencia])
                    ->orWhereRaw("to_char(fecha_inicio, 'DD/MM/YYYY HH24:MI') ILIKE ?", [$coincidencia])
                    ->orWhereRaw("to_char(fecha_fin, 'DD-MM-YYYY HH24:MI') ILIKE ?", [$coincidencia])
                    ->orWhereRaw("to_char(fecha_fin, 'DD/MM/YYYY HH24:MI') ILIKE ?", [$coincidencia])
                    ->orWhere('titulo', 'ILIKE', $coincidencia)
                    ->orWhere('requerimientos_especiales', 'ILIKE', $coincidencia)
                    ->orWhere('nombre_autoridad', 'ILIKE', $coincidencia)
                    ->orWhere('responsable', 'ILIKE', $coincidencia)
                    ->orWhere('tipo_evento', 'ILIKE', $coincidencia)
                    ->orWhereRaw('CAST(aforo AS TEXT) ILIKE ?', [$coincidencia])
                    ->orWhere('tipo_ingreso', 'ILIKE', $coincidencia)
                    ->orWhere('registrado_por', 'ILIKE', $coincidencia)
                    ->orWhere('nombre_usuario', 'ILIKE', $coincidencia)
                    ->orWhereRaw("to_char(fecha_creacion, 'DD/MM/YY HH24:MI') ILIKE ?", [$coincidencia]);

                if ($buscarFechaNumerica) {
                    $fechaCompacta = '%'.$digitosFecha.'%';
                    $query->orWhereRaw("to_char(fecha_inicio, 'DDMMYYYYHH24MI') ILIKE ?", [$fechaCompacta])
                        ->orWhereRaw("to_char(fecha_inicio, 'YYYYMMDDHH24MI') ILIKE ?", [$fechaCompacta])
                        ->orWhereRaw("to_char(fecha_fin, 'DDMMYYYYHH24MI') ILIKE ?", [$fechaCompacta])
                        ->orWhereRaw("to_char(fecha_fin, 'YYYYMMDDHH24MI') ILIKE ?", [$fechaCompacta]);
                }
            });
        };

        $crearConsultaFiltrada = function (array $excluir = []) use (
            $salonSeleccionado,
            $responsableSeleccionado,
            $autoridadSeleccionada,
            $fechaSeleccionada,
            $aplicarBusqueda
        ) {
            $consulta = Reserva::query();

            if (!in_array('salon', $excluir, true) && is_string($salonSeleccionado) && $salonSeleccionado !== '') {
                $consulta->where('salon', $salonSeleccionado);
            }

            if (!in_array('responsable', $excluir, true) && is_string($responsableSeleccionado) && $responsableSeleccionado !== '') {
                $consulta->where('responsable', $responsableSeleccionado);
            }

            if (!in_array('autoridad', $excluir, true) && is_string($autoridadSeleccionada) && $autoridadSeleccionada !== '') {
                $consulta->where('nombre_autoridad', $autoridadSeleccionada);
            }

            if (is_string($fechaSeleccionada) && $fechaSeleccionada !== '') {
                $consulta->whereDate('fecha_inicio', $fechaSeleccionada);
            }

            $aplicarBusqueda($consulta);

            return $consulta;
        };

        // Cada lista toma en cuenta la fecha, la búsqueda y los demás filtros activos.
        $hayFiltrosParaSalones = $buscar !== ''
            || (is_string($responsableSeleccionado) && $responsableSeleccionado !== '')
            || (is_string($autoridadSeleccionada) && $autoridadSeleccionada !== '')
            || (is_string($fechaSeleccionada) && $fechaSeleccionada !== '');

        if ($hayFiltrosParaSalones) {
            $nombresSalonesEncontrados = $crearConsultaFiltrada(['salon'])
                ->whereNotNull('salon')
                ->distinct()
                ->pluck('salon');
            $salonesFiltro = $salones
                ->filter(fn ($salon) => $nombresSalonesEncontrados->contains($salon->nombre))
                ->values();
        } else {
            $salonesFiltro = $salones;
        }

        $responsables = $crearConsultaFiltrada(['responsable'])
            ->whereNotNull('responsable')
            ->where('responsable', '!=', '')
            ->distinct()
            ->orderBy('responsable')
            ->pluck('responsable');

        $autoridades = $crearConsultaFiltrada(['autoridad'])
            ->whereNotNull('nombre_autoridad')
            ->where('nombre_autoridad', '!=', '')
            ->distinct()
            ->orderBy('nombre_autoridad')
            ->pluck('nombre_autoridad');

        $consulta = $crearConsultaFiltrada();

        // Conserva el filtro al cambiar de página.
        $reservas = $consulta
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Se conserva la lógica existente del último cambio.
        $ultimoCambio = Reserva::withTrashed()
            ->orderByRaw('COALESCE(fecha_actualizacion, fecha_creacion) DESC')
            ->first();

        return view('salones.supervisar', compact(
            'reservas',
            'salones',
            'salonesFiltro',
            'coloresSalones',
            'salonSeleccionado',
            'responsables',
            'responsableSeleccionado',
            'autoridades',
            'autoridadSeleccionada',
            'fechaSeleccionada',
            'buscar',
            'ultimoCambio'
        ));
    }


    // Crea una reserva desde el calendario.
    public function guardar(Request $request)
    {
        if (!in_array(auth()->user()?->rol, ['admin', 'superadmin'], true)) {
            return response()->json(['success' => false, 'message' => 'Tu cuenta solo permite consultar el calendario.'], 403);
        }
        
        // AHORA VALIDAMOS TODOS LOS CAMPOS DEL FORMULARIO UNIFICADO
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'nombre_usuario' => 'nullable|string',
            'nombre_autoridad' => 'nullable|string',
            'responsable' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos())],
        ]);

        // Solo una reserva existente puede cancelarse desde el selector de superadmin.
        $validated['titulo'] = $this->guardarEstadoEnTitulo($validated['titulo'], 'programado');

        // ==========================================
        // VALIDACIÓN: PREVENIR CRUCE DE HORARIOS
        // ==========================================
        $conflicto = Reserva::where('salon', $validated['salon'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '<=', $validated['fecha_inicio'])
                      ->where('fecha_fin', '>', $validated['fecha_inicio']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '<', $validated['fecha_fin'])
                      ->where('fecha_fin', '>=', $validated['fecha_fin']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '>=', $validated['fecha_inicio'])
                      ->where('fecha_fin', '<=', $validated['fecha_fin']);
                });
            })
            ->first();

        if ($conflicto) {
            return response()->json([
                'success' => false, 
                'message' => 'El salón "' . $validated['salon'] . '" ya está ocupado en ese horario.'
            ], 422);
        }
        // ==========================================

        $validated['modificado_por'] = $this->actorActual();
        Reserva::create($validated);

        // Como unificamos todo, ya no necesitamos redireccionar. JavaScript cerrará el modal solo.
        return response()->json([
            'success' => true
        ]);
    }

    // Devuelve una reserva para completar el formulario de edición.
    public function obtenerReservaApi($id)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede consultar el detalle de edición.');

        $reserva = Reserva::findOrFail($id);
        [$titulo, $estado] = $this->leerEstadoTitulo($reserva->titulo);
        $reserva->setAttribute('titulo', $titulo);
        $reserva->setAttribute('estado', $estado);

        return response()->json($reserva);
    }

    // Actualiza una reserva desde el calendario.
    public function actualizar(Request $request, $id)
    {
        if (auth()->user()?->rol !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Solo el superadministrador puede editar reservas.'], 403);
        }

        $reserva = Reserva::findOrFail($id);

        [, $estadoActual] = $this->leerEstadoTitulo($reserva->titulo);
        $estadoSolicitado = $request->input('estado', $estadoActual);
        $maxTitulo = $estadoSolicitado === 'cancelado' ? 243 : 255;

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:'.$maxTitulo],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'nombre_usuario' => 'nullable|string',
            'telefono_usuario' => 'nullable|string',
            'nombre_autoridad' => 'nullable|string',
            'responsable' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos($reserva))],
        ]);

        if ($request->exists('estado')) {
            $estadoValidado = $request->validate([
                'estado' => ['required', Rule::in(['programado', 'cancelado'])],
            ])['estado'];
        } else {
            $estadoValidado = $estadoActual;
        }
        // Al editar, el prefijo guarda la cancelación dentro del mismo título.
        $validated['titulo'] = $this->guardarEstadoEnTitulo($validated['titulo'], $estadoValidado);

        // VALIDACIÓN DE CRUCE AL EDITAR (Excluyendo el ID actual)
        $conflicto = Reserva::where('salon', $validated['salon'])
            ->where('id', '!=', $id) // <-- Esta línea es vital para que no choque consigo misma
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '<=', $validated['fecha_inicio'])
                      ->where('fecha_fin', '>', $validated['fecha_inicio']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '<', $validated['fecha_fin'])
                      ->where('fecha_fin', '>=', $validated['fecha_fin']);
                })->orWhere(function ($q) use ($validated) {
                    $q->where('fecha_inicio', '>=', $validated['fecha_inicio'])
                      ->where('fecha_fin', '<=', $validated['fecha_fin']);
                });
            })
            ->first();

        if ($conflicto) {
            return response()->json([
                'success' => false, 
                'message' => 'No puedes mover la reserva a este horario. El salón ya está ocupado.'
            ], 422);
        }
        $validated['modificado_por'] = $this->actorActual();
        $reserva->update($validated);

        return response()->json(['success' => true]);
    }

    // Cancela un evento sin permitir que el administrador cambie sus datos.
    public function cancelar($id)
    {
        if (!in_array(auth()->user()?->rol, ['admin', 'superadmin'], true)) {
            return response()->json(['success' => false, 'message' => 'Tu cuenta solo permite consultar el calendario.'], 403);
        }

        $reserva = Reserva::findOrFail($id);
        [, $estado] = $this->leerEstadoTitulo($reserva->titulo);

        if ($estado !== 'cancelado') {
            [$tituloLimpio] = $this->leerEstadoTitulo($reserva->titulo);
            $reserva->titulo = $this->guardarEstadoEnTitulo(mb_substr($tituloLimpio, 0, 243), 'cancelado');
            $reserva->modificado_por = $this->actorActual();
            $reserva->save();
        }

        return response()->json(['success' => true, 'message' => 'El evento fue cancelado.']);
    }

    // Mueve una reserva a la papelera.
    public function eliminar($id)
    {
        if (auth()->user()?->rol !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Solo el superadministrador puede eliminar reservas.'], 403);
        }

        $reserva = Reserva::findOrFail($id);
        $reserva->modificado_por = $this->actorActual();
        $reserva->save();
        $reserva->delete();

        return response()->json(['success' => true]);
    }

    // Abre el formulario completo de una reserva.
    public function mostrarProtocolo($id)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede consultar protocolos completos.');

        $reserva = Reserva::findOrFail($id);
        $salones = Salon::orderBy('orden')->get();

        return view('salones.protocolo', compact('reserva', 'salones'));
    }

    // Guarda el protocolo y vuelve al calendario.
    public function actualizarProtocolo(Request $request, $id)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede modificar protocolos.');

        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos($reserva))],
            'nombre_autoridad' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'coordinador_interno' => 'nullable|string',
        ]);

        [, $estadoActual] = $this->leerEstadoTitulo($reserva->titulo);
        $validated['titulo'] = $this->guardarEstadoEnTitulo($validated['titulo'], $estadoActual);

        $validated['modificado_por'] = $this->actorActual();
        $reserva->update($validated);

        return redirect()->route('salones.usar')->with('success', 'Reserva completada con éxito');
    }
    // Lista las reservas enviadas a la papelera.
    public function papelera(Request $request)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede administrar la papelera.');

        $salones = Salon::orderBy('orden')->orderBy('nombre')->get();
        $salonSeleccionado = $request->query('salon');
        $consulta = Reserva::onlyTrashed();
        if (is_string($salonSeleccionado) && $salonSeleccionado !== '') {
            $consulta->where('salon', $salonSeleccionado);
        }
        $reservas = $consulta->orderBy('eliminado_en', 'desc')->paginate(5)->withQueryString();

        return view('salones.papelera', compact('reservas', 'salones', 'salonSeleccionado'));
    }

    // Elimina de forma permanente las reservas de la papelera.
    public function vaciarPapelera()
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede administrar la papelera.');
        
        Reserva::onlyTrashed()->forceDelete();
        return back();
    }
    // Devuelve una reserva de la papelera.
    public function restaurarPapelera($id)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede administrar la papelera.');

        $reserva = Reserva::onlyTrashed()->findOrFail($id);
        $reserva->modificado_por = $this->actorActual();
        $reserva->restore();

        return back();
    }

    // Muestra el historial paginado de reservas.
    public function supervisarReservas()
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede consultar el historial.');

        $reservas = Reserva::orderBy('fecha_creacion', 'desc')->paginate(10);
        $totalFormularios = Reserva::count();
        // Cambiar el nombre aquí y en el compact:
        $ultimoEvento = Reserva::orderBy('fecha_creacion', 'desc')->first();

        return view('salones.supervisar_reservas', compact('reservas', 'totalFormularios', 'ultimoEvento'));
    }
    // Abre el formulario completo de creación.
    public function mostrarFormularioCrear()
    {
        abort_unless(in_array(auth()->user()?->rol, ['admin', 'superadmin'], true), 403, 'Tu cuenta solo permite consultar el calendario.');

        return view('salones.crear_reserva', ['salones' => Salon::where('activo', true)->orderBy('orden')->get()]);
    }

    // Crea una reserva con el horario inicial del formulario.
    public function guardarReservaCompleta(Request $request)
    {
        abort_unless(in_array(auth()->user()?->rol, ['admin', 'superadmin'], true), 403, 'Tu cuenta solo permite consultar el calendario.');

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'nombre_autoridad' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'coordinador_interno' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos())],
        ]);

        $validated['fecha_inicio'] = now();
        $validated['fecha_fin'] = now()->addHour();
        $validated['nombre_usuario'] = auth()->user()->nombre ?? 'Administrador';
        $validated['modificado_por'] = $this->actorActual();
        $validated['titulo'] = $this->guardarEstadoEnTitulo($validated['titulo'], 'programado');

        Reserva::create($validated);

        return redirect()->route('reservas.supervisar')->with('success', 'Reserva creada con éxito');
    }
    // Exporta las reservas a CSV aplicando el filtro de salón si existe
    public function exportarCsv(Request $request)
    {
        abort_unless(auth()->user()?->rol === 'superadmin', 403, 'Solo el superadministrador puede exportar las reservas.');

        // 1. Obtener el filtro de la URL
        $salonSeleccionado = $request->query('salon');
        
        // 2. Construir la consulta
        $consulta = Reserva::query();
        if (is_string($salonSeleccionado) && $salonSeleccionado !== '') {
            $consulta->where('salon', $salonSeleccionado);
        }

        // Ordenamos por fecha para que el Excel tenga sentido
        $reservas = $consulta->orderBy('fecha_inicio', 'asc')->get();

        // 3. Crear el nombre del archivo
        $nombreArchivo = 'reservas_';
        $nombreArchivo .= $salonSeleccionado ? str_replace(' ', '_', $salonSeleccionado) : 'todos_los_salones';
        $nombreArchivo .= '_' . date('Y-m-d_H-i') . '.csv';

        // 4. Preparar la respuesta HTTP para forzar la descarga del CSV
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$nombreArchivo",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 5. Generar el contenido del CSV
        $callback = function() use($reservas) {
            $file = fopen('php://output', 'w');
            
            // Para que Excel lea correctamente los acentos (BOM UTF-8)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Escribir la cabecera (nombres de las columnas)
            fputcsv($file, [
                'Salón', 
                'Título del Evento', 
                'Fecha Inicio', 
                'Fecha Fin', 
                'Responsable', 
                'Autoridad',
                'Tipo de Evento',
                'Aforo',
                'Creado por',
                'Fecha de Creación'
            ], ';'); // Usamos punto y coma para que Excel lo separe en columnas fácilmente

            // Escribir los datos fila por fila
            foreach ($reservas as $reserva) {
                fputcsv($file, [
                    $reserva->salon,
                    $reserva->titulo,
                    $reserva->fecha_inicio,
                    $reserva->fecha_fin,
                    $reserva->responsable,
                    $reserva->nombre_autoridad,
                    $reserva->tipo_evento,
                    $reserva->aforo,
                    $reserva->nombre_usuario,
                    $reserva->fecha_creacion
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
