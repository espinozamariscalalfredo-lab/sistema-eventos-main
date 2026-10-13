<?php

use Illuminate\Database\Migrations\Migration;

// Las tareas usan el controlador sincrono; no se crean tablas de colas en PostgreSQL.
return new class extends Migration
{
    public function up(): void {}

    public function down(): void {}
};
