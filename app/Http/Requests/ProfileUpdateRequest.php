<?php

// BACKEND/VALIDACIÓN. 20/10/2026: reglas del formulario de perfil antes de guardar.
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /** Devuelve las reglas para validar los cambios del perfil.
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('usuarios', 'correo')->ignore($this->user()->id),
            ],
        ];
    }
}
