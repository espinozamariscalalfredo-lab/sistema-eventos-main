<?php

namespace App\Models;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Conecta la autenticación de Laravel con la tabla usuarios en español.
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, Notifiable;

    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'correo', 'telefono', 'rol', 'contrasena'];

    protected $hidden = ['contrasena', 'token_recordar'];

    protected function casts(): array
    {
        return [
            'correo_verificado_en' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    // Laravel consulta estos metodos para verificacion; la base guarda el dato en espanol.
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->correo_verificado_en);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill(['correo_verificado_en' => $this->freshTimestamp()])->save();
    }

    public function markEmailAsUnverified(): bool
    {
        return $this->forceFill(['correo_verificado_en' => null])->save();
    }

    // Las notificaciones de Laravel usan esta ruta para obtener el correo de destino.
    public function routeNotificationForMail($notification = null): string
    {
        return $this->correo;
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function getRememberTokenName(): string
    {
        return 'token_recordar';
    }

    public function getEmailForVerification(): string
    {
        return $this->correo;
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }
}
