<?php

namespace App\Database\Autenticacion;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as PuedeRestablecerContrasena;
use Illuminate\Support\Carbon;

/** Guarda tokens de recuperación en columnas españolas. */
class RepositorioTokensEspanol extends DatabaseTokenRepository
{
    protected function deleteExisting(PuedeRestablecerContrasena $usuario)
    {
        return $this->getTable()->where('correo', $usuario->getEmailForPasswordReset())->delete();
    }

    protected function getPayload($correo, #[\SensitiveParameter] $token)
    {
        return [
            'correo' => $correo,
            'token' => $this->hasher->make($token),
            'fecha_creacion' => new Carbon(),
        ];
    }

    public function exists(PuedeRestablecerContrasena $usuario, #[\SensitiveParameter] $token)
    {
        $registro = (array) $this->getTable()
            ->where('correo', $usuario->getEmailForPasswordReset())
            ->first();

        return $registro
            && ! $this->tokenExpired($registro['fecha_creacion'])
            && $this->hasher->check($token, $registro['token']);
    }

    public function recentlyCreatedToken(PuedeRestablecerContrasena $usuario)
    {
        $registro = (array) $this->getTable()
            ->where('correo', $usuario->getEmailForPasswordReset())
            ->first();

        return $registro && $this->tokenRecentlyCreated($registro['fecha_creacion']);
    }

    public function deleteExpired()
    {
        $expiradoAntesDe = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('fecha_creacion', '<', $expiradoAntesDe)->delete();
    }
}
