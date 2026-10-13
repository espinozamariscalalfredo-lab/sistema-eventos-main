<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cambio 12/10/2026: registra en PostgreSQL quién creó o modificó cada reserva.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('modificado_por')->nullable();
        });
    }

    // Se conserva la columna para preservar la información de auditoría.
    public function down(): void
    {
        throw new LogicException('La columna modificado_por se conserva para mantener la auditoría.');
    }
};
