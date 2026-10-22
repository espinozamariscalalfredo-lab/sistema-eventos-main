<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablasVaciasNoUsadas = [
        'almacenamiento_cache',
        'bloqueos_cache',
        'lotes_trabajos',
        'protocolos',
        'sesiones',
        'trabajos',
        'trabajos_fallidos',
        'x',
    ];

    public function up(): void
    {
        foreach ($this->tablasVaciasNoUsadas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            // La aplicación usa sesiones en archivos; reviso la configuración antes de borrar esta tabla.
            if ($tabla === 'sesiones') {
                if (config('session.driver') !== 'file') {
                    throw new RuntimeException('No se elimina sesiones mientras la aplicación use sesiones en PostgreSQL.');
                }

                Schema::drop($tabla);
                continue;
            }

            if (DB::table($tabla)->exists()) {
                throw new RuntimeException("Se conserva la tabla {$tabla}: contiene datos y requiere revisión.");
            }

            Schema::drop($tabla);
        }
    }

    public function down(): void
    {
        throw new LogicException('Las tablas auxiliares se retiraron intencionalmente; restaurarlas requiere revisar el controlador de cada servicio.');
    }
};
