<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Esquema espanol para usuarios; tokens de recuperacion se mantienen en el formato que Laravel requiere.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('telefono')->nullable();
            $table->string('rol')->default('funcionario');
            $table->timestamp('correo_verificado_en')->nullable();
            $table->string('contrasena');
            $table->string('token_recordar', 100)->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
        });

        Schema::create('tokens_restablecimiento', function (Blueprint $table) {
            $table->string('correo')->primary();
            $table->string('token');
            $table->timestamp('fecha_creacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens_restablecimiento');
        Schema::dropIfExists('usuarios');
    }
};
