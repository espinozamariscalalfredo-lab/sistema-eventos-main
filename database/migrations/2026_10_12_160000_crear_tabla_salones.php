<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cambio 12/10/2026: catalogo persistente de salones administrado desde la aplicacion.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('color', 7);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
        });
    }

    // No se permite revertir esta migracion porque el catalogo puede tener datos administrados.
    public function down(): void
    {
        throw new LogicException('La tabla salones se conserva; no se elimina durante un rollback.');
    }
};