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

        $reservas = Reserva::all()->map(function ($reserva) use ($salonColors) {
            // Las reservas antiguas sin salón usan un color de respaldo.
            $color = $salonColors[$reserva->salon] ?? '#f59e0b';

            return [
                // FullCalendar requiere estas tres claves en inglés.
                'id' => $reserva->id,
                'title' => $reserva->titulo,
                'start' => $reserva->fecha_inicio,
                'end' => $reserva->fecha_fin,
                'display' => 'block',
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                // Los campos adicionales se usan en el detalle y la edición.
                'nombre_usuario' => $reserva->nombre_usuario,
                'telefono_usuario' => $reserva->telefono_usuario,
                'nombre_autoridad' => $reserva->nombre_autoridad,
                'responsable' => $reserva->responsable,
                'nombre_coordinador_externo' => $reserva->nombre_coordinador_externo,
                'telefono_coordinador_externo' => $reserva->telefono_coordinador_externo,
                'tipo_evento' => $reserva->tipo_evento,
                'aforo' => $reserva->aforo,
                'tipo_ingreso' => $reserva->tipo_ingreso,
                'requerimientos' => $reserva->requerimientos,
                'requerimientos_especiales' => $reserva->requerimientos_especiales,
                'registrado_por' => $reserva->registrado_por,
                'coordinador_interno' => $reserva->coordinador_interno,
                'modificado_por' => $reserva->modificado_por,
                'salon' => $reserva->salon,
                'fecha_creacion' => $reserva->fecha_creacion,
            ];
        });

        return response()->json($reservas);
    }

    // Lista reservas y aplica el filtro de salón.
    public function supervisar(Request $request)
    {
        // El mismo catálogo PostgreSQL alimenta el filtro de la lista y el de la papelera.
        $salones = Salon::orderBy('orden')->orderBy('nombre')->get();
        $salonSeleccionado = $request->query('salon');
        $consulta = Reserva::query();
        if (is_string($salonSeleccionado) && $salonSeleccionado !== '') {
            $consulta->where('salon', $salonSeleccionado);
        }

        $reservas = $consulta->orderBy('fecha_inicio', 'desc')->paginate(10)->withQueryString();
        $ultimoCambio = Reserva::withTrashed()
            ->orderByRaw('COALESCE(fecha_actualizacion, fecha_creacion) DESC')
            ->first();
        return view('salones.supervisar', compact('reservas', 'salones', 'salonSeleccionado', 'ultimoCambio'));
    }

    // Crea una reserva desde el calendario.
    public function guardar(Request $request)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'superadmin'], true)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para crear.'], 403);
        }
        
        // AHORA VALIDAMOS TODOS LOS CAMPOS DEL FORMULARIO UNIFICADO
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'nombre_usuario' => 'nullable|string',
            'nombre_autoridad' => 'nullable|string',
            'responsable' => 'nullable|string',
            'nombre_coordinador_externo' => 'nullable|string',
            'telefono_coordinador_externo' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos())],
        ]);

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
        $reserva = Reserva::findOrFail($id);

        return response()->json($reserva);
    }

    // Actualiza una reserva desde el calendario.
    public function actualizar(Request $request, $id)
    {
        if (!in_array(auth()->user()->rol, ['admin', 'superadmin'], true)) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para editar.'], 403);
        }

        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'nombre_usuario' => 'nullable|string',
            'telefono_usuario' => 'nullable|string',
            'nombre_autoridad' => 'nullable|string',
            'responsable' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos($reserva))],
        ]);

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

    // Mueve una reserva a la papelera.
    public function eliminar($id)
    {
        if (auth()->user()->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Solo el Admin puede eliminar reservas.'], 403);
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
        $reserva = Reserva::findOrFail($id);
        $salones = Salon::orderBy('orden')->get();

        return view('salones.protocolo', compact('reserva', 'salones'));
    }

    // Guarda el protocolo y vuelve al calendario.
    public function actualizarProtocolo(Request $request, $id)
    {
        if (auth()->user()->rol === 'usuario' || empty(auth()->user()->rol)) {
            abort(403, 'No tienes permiso para modificar los protocolos.');
        }

        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos($reserva))],
            'nombre_autoridad' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'nombre_coordinador_externo' => 'nullable|string',
            'telefono_coordinador_externo' => 'nullable|string',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'coordinador_interno' => 'nullable|string',
        ]);

        $validated['modificado_por'] = $this->actorActual();
        $reserva->update($validated);

        return redirect()->route('salones.usar')->with('success', 'Reserva completada con éxito');
    }
    // Lista las reservas enviadas a la papelera.
    public function papelera(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403, 'Acceso denegado.');

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
        if (auth()->user()->rol !== 'admin') abort(403, 'Acceso denegado.');
        
        Reserva::onlyTrashed()->forceDelete();
        return back();
    }
    // Devuelve una reserva de la papelera.
    public function restaurarPapelera($id)
    {
        if (auth()->user()->rol === 'usuario' || empty(auth()->user()->rol)) {
            abort(403, 'Acceso denegado.');
        }

        $reserva = Reserva::onlyTrashed()->findOrFail($id);
        $reserva->modificado_por = $this->actorActual();
        $reserva->restore();

        return back();
    }

    // Muestra el historial paginado de reservas.
    public function supervisarReservas()
    {
        $reservas = Reserva::orderBy('fecha_creacion', 'desc')->paginate(10);
        $totalFormularios = Reserva::count();
        // Cambiar el nombre aquí y en el compact:
        $ultimoEvento = Reserva::orderBy('fecha_creacion', 'desc')->first();

        return view('salones.supervisar_reservas', compact('reservas', 'totalFormularios', 'ultimoEvento'));
    }
    // Abre el formulario completo de creación.
    public function mostrarFormularioCrear()
    {
        return view('salones.crear_reserva', ['salones' => Salon::where('activo', true)->orderBy('orden')->get()]);
    }

    // Crea una reserva con el horario inicial del formulario.
    public function guardarReservaCompleta(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'nombre_autoridad' => 'nullable|string',
            'tipo_evento' => 'nullable|string',
            'requerimientos' => 'nullable|array',
            'aforo' => 'nullable|integer',
            'tipo_ingreso' => 'nullable|string',
            'nombre_coordinador_externo' => 'nullable|string',
            'telefono_coordinador_externo' => 'nullable|string',
            'requerimientos_especiales' => 'nullable|string',
            'registrado_por' => 'nullable|string',
            'coordinador_interno' => 'nullable|string',
            'salon' => ['required', 'string', Rule::in($this->salonesValidos())],
        ]);

        $validated['fecha_inicio'] = now();
        $validated['fecha_fin'] = now()->addHour();
        $validated['nombre_usuario'] = auth()->user()->nombre ?? 'Administrador';
        $validated['modificado_por'] = $this->actorActual();

        Reserva::create($validated);

        return redirect()->route('reservas.supervisar')->with('success', 'Reserva creada con éxito');
    }
}
