<?php

namespace App\Database\Autenticacion;

use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

/** Conecta la recuperación de contraseña con PostgreSQL en español. */
class GestorRestablecimientoEspanol extends PasswordBrokerManager
{
    protected function createTokenRepository(array $config): TokenRepositoryInterface
    {
        $clave = $this->app['config']['app.key'];

        if (str_starts_with($clave, 'base64:')) {
            $clave = base64_decode(substr($clave, 7));
        }

        return new RepositorioTokensEspanol(
            $this->app['db']->connection($config['connection'] ?? null),
            $this->app['hash'],
            $config['table'],
            $clave,
            ($config['expire'] ?? 60) * 60,
            $config['throttle'] ?? 0,
        );
    }
}
