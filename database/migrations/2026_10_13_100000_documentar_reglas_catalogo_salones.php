<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Cambio 13/10/2026: documenta las reglas del catálogo de salones.
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('salones')) {
            return;
        }

        $textoTabla = 'Catálogo del calendario; los cambios se validan en la aplicación. Cambio 13/10/2026.';
        DB::statement(
            'COMMENT ON TABLE pro_salones IS ' . DB::connection()->getPdo()->quote($textoTabla)
        );

        if (Schema::hasColumn('salones', 'color')) {
            $textoColor = 'Color fijo desde su creación; la aplicación impide repetirlo. Cambio 13/10/2026.';
            DB::statement(
                'COMMENT ON COLUMN pro_salones.color IS ' . DB::connection()->getPdo()->quote($textoColor)
            );
        }

        if (Schema::hasColumn('salones', 'activo')) {
            $textoEstado = 'Indica si se ofrece para nuevas reservas; la aplicación bloquea la baja si hay reservas. Cambio 13/10/2026.';
            DB::statement(
                'COMMENT ON COLUMN pro_salones.activo IS ' . DB::connection()->getPdo()->quote($textoEstado)
            );
        }
    }

    public function down(): void
    {
        // Se conservan los comentarios que explican las reglas del catálogo.
    }
};
