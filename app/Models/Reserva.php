<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Cambio al español: 12/10/2026. Representa la tabla reservas y sus campos en español.
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
        'aforo', 'tipo_ingreso', 'nombre_coordinador_externo',
        'telefono_coordinador_externo', 'requerimientos_especiales', 'registrado_por',
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
}
