<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Una reserva conserva sus datos originales y se relaciona con el salón y los usuarios.
class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_actualizacion';
    public const DELETED_AT = 'eliminado_en';

    protected $table = 'reservas';

    protected $fillable = [
        'titulo', 'fecha_inicio', 'fecha_fin', 'nombre_usuario', 'telefono_usuario',
        'nombre_autoridad', 'responsable', 'salon', 'tipo_evento', 'requerimientos',
        'aforo', 'tipo_ingreso', 'requerimientos_especiales', 'registrado_por',
        'coordinador_interno', 'modificado_por',
    ];

    protected $casts = [
        'requerimientos' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'eliminado_en' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $reserva): void {
            // Mantengo el nombre del salón para las pantallas y uso el ID en la relación.
            $reserva->salon_id = $reserva->salon
                ? Salon::query()->where('nombre', $reserva->salon)->value('id')
                : null;

            $usuarioId = auth()->id();
            if ($usuarioId !== null) {
                if (! $reserva->exists) {
                    $reserva->registrado_por_usuario_id ??= $usuarioId;
                }

                $reserva->modificado_por_usuario_id = $usuarioId;
            }
        });
    }

    public function salonCatalogo(): BelongsTo
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }

    public function usuarioRegistrador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por_usuario_id');
    }

    public function usuarioModificador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'modificado_por_usuario_id');
    }
}
