<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Catálogo de salones disponible para las reservas.
class Salon extends Model
{
    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_actualizacion';

    protected $table = 'salones';

    protected $fillable = ['nombre', 'color', 'activo', 'orden'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'salon_id');
    }
}
