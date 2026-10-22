<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tablas = [
        'almacenamiento_cache',
        'bloqueos_cache',
        'eventos',
        'historial_migraciones',
        'lotes_trabajos',
        'protocolos',
        'reservas',
        'salones',
        'sesiones',
        'tokens_restablecimiento',
        'trabajos',
        'trabajos_fallidos',
        'usuarios',
        'x',
    ];

    private array $secuencias = [
        'historial_migraciones_id_migracion_seq',
        'reservas_id_seq',
        'salones_id_seq',
        'usuarios_id_seq',
    ];

    public function up(): void
    {
        // Renombro tablas y secuencias en el esquema activo para aplicar el mismo prefijo.
        $schema = DB::selectOne('SELECT current_schema() AS nombre')?->nombre;
        if (! is_string($schema) || ! preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $schema)) {
            throw new RuntimeException('No se pudo identificar el esquema PostgreSQL para renombrar las tablas.');
        }

        foreach ($this->tablas as $tabla) {
            $this->renombrarSiExiste($schema, $tabla, 'pro_'.$tabla);
        }

        foreach ($this->secuencias as $secuencia) {
            $this->renombrarSecuenciaSiExiste($schema, $secuencia, 'pro_'.$secuencia);
        }
    }

    public function down(): void
    {
        // La aplicación ya consulta con pro_; revertirlo requiere cambiar también esa configuración.
        throw new LogicException('Los nombres pro_ son parte de la configuración activa del sistema y no se revierten automáticamente.');
    }

    private function renombrarSiExiste(string $schema, string $origen, string $destino): void
    {
        $origenExiste = DB::selectOne('SELECT to_regclass(?) AS tabla', [$schema.'.'.$origen])?->tabla;
        $destinoExiste = DB::selectOne('SELECT to_regclass(?) AS tabla', [$schema.'.'.$destino])?->tabla;

        if ($origenExiste && $destinoExiste) {
            // Detengo la migración para evitar sobrescribir una tabla que ya existe.
            throw new RuntimeException("No se renombró {$origen}: ya existe {$destino}.");
        }

        if ($origenExiste) {
            DB::statement('ALTER TABLE "'.$schema.'"."'.$origen.'" RENAME TO "'.$destino.'"');
        }
    }

    private function renombrarSecuenciaSiExiste(string $schema, string $origen, string $destino): void
    {
        $origenExiste = DB::selectOne('SELECT to_regclass(?) AS secuencia', [$schema.'.'.$origen])?->secuencia;
        $destinoExiste = DB::selectOne('SELECT to_regclass(?) AS secuencia', [$schema.'.'.$destino])?->secuencia;

        if ($origenExiste && $destinoExiste) {
            // Detengo la migración para evitar sobrescribir una secuencia que ya existe.
            throw new RuntimeException("No se renombró {$origen}: ya existe {$destino}.");
        }

        if ($origenExiste) {
            DB::statement('ALTER SEQUENCE "'.$schema.'"."'.$origen.'" RENAME TO "'.$destino.'"');
        }
    }
};
