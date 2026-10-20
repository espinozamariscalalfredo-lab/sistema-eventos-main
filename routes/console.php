<?php

// BACKEND. 20/10/2026: aquí se declaran comandos propios de Artisan, no direcciones web.
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
