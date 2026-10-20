<?php

// BASE DE DATOS. 20/10/2026: carga inicial y cuenta de demostración para desarrollo.
namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario::factory(10)->create();

        Usuario::factory()->create([
            'nombre' => 'Usuario de prueba',
            'correo' => 'prueba@example.com',
        ]);
    }
}
