<?php

use Illuminate\Database\Migrations\Migration;

// El cache del proyecto se guarda en archivos; no se crean tablas de cache en PostgreSQL.
return new class extends Migration
{
    public function up(): void {}

    public function down(): void {}
};
