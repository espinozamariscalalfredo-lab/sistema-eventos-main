<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_coordinador_externo',
                'telefono_coordinador_externo',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('nombre_coordinador_externo')->nullable();
            $table->string('telefono_coordinador_externo')->nullable();
        });
    }
};
