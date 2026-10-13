<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Contraseña temporal compartida por los datos de prueba.
     */
    protected static ?string $password;

    /**
     * Crea datos de prueba para una cuenta.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'correo' => fake()->unique()->safeEmail(),
            'correo_verificado_en' => now(),
            'contrasena' => static::$password ??= Hash::make('password'),
            'token_recordar' => Str::random(10),
        ];
    }

    /**
     * Deja el correo sin verificar en los datos de prueba.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'correo_verificado_en' => null,
        ]);
    }
}
