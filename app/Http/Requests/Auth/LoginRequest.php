<?php

// BACKEND/AUTENTICACIÓN. 20/10/2026: valida y limita intentos del formulario de ingreso.
namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    // Permite enviar la solicitud de ingreso.
    public function authorize(): bool
    {
        return true;
    }

    /** Reglas para validar el correo y la contraseña. */
    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /** @throws ValidationException */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Solo la opción "keep" mantiene abierta la sesión.
        $preferencia = $this->input('session_preference');
        $recordarSesion = ($preferencia === 'keep');

        if (! Auth::attempt($this->only('correo', 'password'), $recordarSesion)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'correo' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /** Bloquea intentos repetidos de ingreso. @throws ValidationException */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'correo' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /** Crea la clave para contar intentos por correo e IP. */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('correo')).'|'.$this->ip());
    }
}
