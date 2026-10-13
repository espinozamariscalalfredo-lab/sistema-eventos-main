<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Conecta el catálogo de salones con PostgreSQL.
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
}
