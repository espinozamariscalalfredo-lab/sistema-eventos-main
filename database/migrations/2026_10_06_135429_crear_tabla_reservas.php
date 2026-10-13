<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cambio de nombres al espanol: 12/10/2026.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('nombre_usuario')->nullable();
            $table->string('telefono_usuario')->nullable();
            $table->string('nombre_autoridad')->nullable();
            $table->string('responsable')->nullable();
            $table->string('salon');
            $table->string('tipo_evento')->nullable();
            $table->json('requerimientos')->nullable();
            $table->integer('aforo')->nullable();
            $table->string('tipo_ingreso')->nullable();
            $table->string('nombre_coordinador_externo')->nullable();
            $table->string('telefono_coordinador_externo')->nullable();
            $table->text('requerimientos_especiales')->nullable();
            $table->string('registrado_por')->nullable();
            $table->string('coordinador_interno')->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
