<?php

namespace App\Providers;

use App\Database\Autenticacion\GestorRestablecimientoEspanol;
use App\Database\Migraciones\RepositorioMigracionesEspanol;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Registra adaptadores para que Laravel use las columnas españolas.
    public function register(): void
    {
        // Estos adaptadores conectan los nombres internos de Laravel con PostgreSQL.
        $this->app->singleton('migration.repository', function ($app) {
            return new RepositorioMigracionesEspanol(
                $app['db'],
                $app['config']['database.migrations.table']
            );
        });

        $this->app->singleton('auth.password', function ($app) {
            return new GestorRestablecimientoEspanol($app);
        });
    }
}
