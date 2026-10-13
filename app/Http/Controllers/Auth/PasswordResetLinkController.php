<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // Muestra el formulario para pedir un enlace de recuperación.
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** @throws ValidationException */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        // Envía el enlace y muestra el resultado.
        $status = Password::sendResetLink(
            $request->only('correo')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('correo'))
                        ->withErrors(['correo' => __($status)]);
    }
}
