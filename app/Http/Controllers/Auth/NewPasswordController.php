<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    // Muestra el formulario para crear una contraseña nueva.
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /** @throws ValidationException */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'correo' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Valida el token y guarda la nueva contraseña.
        $status = Password::reset(
            $request->only('correo', 'password', 'password_confirmation', 'token'),
            function (Usuario $usuario) use ($request) {
                $usuario->forceFill([
                    'contrasena' => Hash::make($request->password),
                    'token_recordar' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            }
        );

        // Muestra confirmación o errores de validación.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('correo'))
                        ->withErrors(['correo' => __($status)]);
    }
}
