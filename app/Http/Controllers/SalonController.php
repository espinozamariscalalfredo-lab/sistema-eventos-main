<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

// Administra el catálogo PostgreSQL que comparten las pantallas de salones.
// Cambio documentado: 13/10/2026. El catálogo define nombres, colores y estado.
class SalonController extends Controller
{
    // Solo el superadmin puede mantener los nombres usados por el calendario.
    // El color se valida al crear y queda fijo después.
    public function guardarNombre(Request $request)
    {
        $this->validarSuperadmin();
        $request->merge(['color' => strtoupper((string) $request->input('color'))]);
        $datos = $request->validate([
            'nombre' => 'required|string|max:120|unique:salones,nombre',
            'color' => [
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
                function ($attribute, $value, $fail) {
                    if (Salon::whereRaw('LOWER(color) = ?', [strtolower($value)])->exists()) {
                        $fail('Ese color ya está asignado a otro nombre. Elige uno diferente.');
                    }
                },
            ],
            'orden' => 'nullable|integer|min:0|max:9999',
        ]);

        Salon::create($datos + ['activo' => true, 'orden' => $datos['orden'] ?? 0]);

        return back()->with('estado_catalogo', 'Nombre agregado al calendario.');
    }

    public function destroy($id)
    {
        // 1. Encontramos y eliminamos el salón
        $salon = Salon::findOrFail($id);
        $salon->delete();

        // 2. Obtenemos todos los salones restantes, ordenados por su 'orden' actual
        $salonesRestantes = Salon::orderBy('orden', 'asc')->get();

        // 3. Recalculamos el orden desde 0 en adelante (0, 1, 2, 3...)
        foreach ($salonesRestantes as $index => $salonRestante) {
            $salonRestante->orden = $index;
            $salonRestante->save();
        }

        return redirect()->back()->with('estado_catalogo', 'Salón eliminado y el orden ha sido recalculado exitosamente.');
    }
    // Renombra el catálogo y sus reservas en una sola transacción.
    public function actualizarNombre(Request $request, Salon $salon)
    {
        $this->validarSuperadmin();
        $datos = $request->validate([
            'nombre' => 'required|string|max:120|unique:salones,nombre,' . $salon->id,
            'orden' => 'required|integer|min:0|max:9999',
        ]);
        DB::transaction(function () use ($salon, $datos) {
            $nombreAnterior = $salon->nombre;
            $salon->update($datos);

            // También actualiza eventos que están en la papelera.
            if ($nombreAnterior !== $salon->nombre) {
                Reserva::withTrashed()
                    ->where('salon', $nombreAnterior)
                    ->update(['salon' => $salon->nombre]);
            }
        });

        return back()->with('estado_catalogo', 'Nombre actualizado.');
    }

    // Da de baja solo los nombres que aún no tienen reservas.
    public function cambiarEstado(Salon $salon)
    {
        $this->validarSuperadmin();
        // No permite dar de baja un nombre usado por reservas actuales o eliminadas.
        if ($salon->activo && Reserva::withTrashed()->where('salon', $salon->nombre)->exists()) {
            return back()->with('error_catalogo', 'No se puede dar de baja: este nombre tiene reservas asociadas.');
        }

        $salon->activo = ! $salon->activo;
        $salon->save();

        return back()->with('estado_catalogo', $salon->activo ? 'Nombre activado.' : 'Nombre dado de baja.');
    }

    private function validarSuperadmin(): void
    {
        abort_unless(in_array(auth()->user()?->rol, ['admin', 'superadmin'], true), 403, 'Solo el admin y superadmin pueden administrar estos nombres.');
    }

    public function calendario(): View
    {
        // La vista usa la lista completa para leyendas y solo ofrece los activos para reservar.
        $salones = Salon::orderBy('orden')->orderBy('nombre')->get();
        $salonesActivos = $salones->where('activo', true)->values();
        $coloresSalones = $salones->pluck('color', 'nombre');

        return view('salones.usar', compact('salones', 'salonesActivos', 'coloresSalones'));
    }

    // El mini calendario recibe los días con reservas para el mes visible.
    public function fechasConReservas(Request $request)
    {
        $mes = $request->input('month', date('m'));
        $anio = $request->input('year', date('Y'));
        $salon = $request->input('salon');

        try {
            $consulta = Reserva::whereMonth('fecha_inicio', $mes)
                ->whereYear('fecha_inicio', $anio);

            if ($salon !== null) {
                if (! is_string($salon) || ! Salon::where('nombre', $salon)->exists()) {
                    return response()->json([]);
                }

                $consulta->where('salon', $salon);
            }

            $fechas = $consulta->selectRaw('DATE(fecha_inicio) as fecha')
                ->distinct()
                ->pluck('fecha');

            return response()->json($fechas);
        } catch (\Exception $error) {
            return response()->json([]);
        }
    }
}
