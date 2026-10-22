<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->foreignId('salon_id')->nullable()->constrained('salones')->nullOnDelete();
            $table->foreignId('registrado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('modificado_por_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->index('salon_id');
            $table->index('registrado_por_usuario_id');
            $table->index('modificado_por_usuario_id');
        });

        // Asocio solo nombres exactos y conservo el texto que ya usan las pantallas.
        DB::statement(<<<'SQL'
            UPDATE pro_reservas AS reserva
            SET salon_id = salon.id
            FROM pro_salones AS salon
            WHERE reserva.salon_id IS NULL
              AND reserva.salon = salon.nombre
        SQL);

        // Los datos anteriores pueden ser nombres o correos; relaciono solo coincidencias únicas.
        DB::statement(<<<'SQL'
            UPDATE pro_reservas AS reserva
            SET registrado_por_usuario_id = usuario.id
            FROM pro_usuarios AS usuario
            WHERE reserva.registrado_por_usuario_id IS NULL
              AND lower(btrim(reserva.registrado_por)) IN (lower(btrim(usuario.nombre)), lower(btrim(usuario.correo)))
              AND 1 = (
                  SELECT count(*)
                  FROM pro_usuarios AS coincidencia
                  WHERE lower(btrim(reserva.registrado_por)) IN (lower(btrim(coincidencia.nombre)), lower(btrim(coincidencia.correo)))
              )
        SQL);

        DB::statement(<<<'SQL'
            UPDATE pro_reservas AS reserva
            SET modificado_por_usuario_id = usuario.id
            FROM pro_usuarios AS usuario
            WHERE reserva.modificado_por_usuario_id IS NULL
              AND lower(btrim(reserva.modificado_por)) IN (lower(btrim(usuario.nombre)), lower(btrim(usuario.correo)))
              AND 1 = (
                  SELECT count(*)
                  FROM pro_usuarios AS coincidencia
                  WHERE lower(btrim(reserva.modificado_por)) IN (lower(btrim(coincidencia.nombre)), lower(btrim(coincidencia.correo)))
              )
        SQL);
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('salon_id');
            $table->dropConstrainedForeignId('registrado_por_usuario_id');
            $table->dropConstrainedForeignId('modificado_por_usuario_id');
        });
    }
};
