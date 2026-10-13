<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Muestra las cuentas y gestiona las acciones permitidas al administrador.
// Cambio documentado: 13/10/2026. Los permisos se comprueban también aquí.
class UsuarioController extends Controller
{
    // Carga cuentas y el catálogo visible para admin y superadmin.
    public function index()
    {
        $usuarios = Usuario::orderBy('id')->get();
        $salones = in_array(auth()->user()->rol, ['admin', 'superadmin'], true)
            ? Salon::orderBy('orden')->orderBy('nombre')->get()
            : collect();

        return view('salones.usuarios', compact('usuarios', 'salones'));
    }

    // Crea una cuenta y guarda su contraseña cifrada.
    public function store(Request $request)
    {
        if (auth()->user()->rol !== 'admin') abort(403, 'Acceso denegado.');

        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'rol' => 'required|string'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->password),
            'rol' => $request->rol,
        ]);

        return back();
    }

    // Actualiza los datos de una cuenta; la contraseña solo cambia si se envía una nueva.
    public function update(Request $request, $id)
    {
        if (auth()->user()->rol !== 'admin') abort(403, 'Acceso denegado.');

        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|string|email|max:255|unique:usuarios,correo,'.$id,
            'rol' => 'required|string'
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->correo = $request->correo;
        $usuario->rol = $request->rol;
        
        // Solo actualizar la contraseña si escribieron una nueva
        if ($request->filled('password')) {
            $usuario->contrasena = Hash::make($request->password);
        }
        
        $usuario->save();

        return back();
    }

    // Elimina una cuenta, excepto la cuenta que inició sesión.
    public function destroy($id)
    {
        if (auth()->user()->rol !== 'admin') abort(403, 'Acceso denegado.');
        
        // Evitar que el admin se borre a sí mismo por accidente
        if (auth()->id() == $id) {
            return back(); 
        }

        Usuario::findOrFail($id)->delete();
        return back();
    }
}
