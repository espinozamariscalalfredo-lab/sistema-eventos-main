<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    // Muestra la confirmación de contraseña.
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    // Verifica la contraseña antes de continuar.
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'correo' => $request->user()->correo,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
