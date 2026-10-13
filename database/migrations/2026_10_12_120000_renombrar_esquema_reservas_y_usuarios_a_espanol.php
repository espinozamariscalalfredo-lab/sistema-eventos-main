<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Migracion 12/10/2026: actualiza una instalacion anterior a los nombres espanoles.
return new class extends Migration
{
    private array $tablas = [
        'auditoriums' => 'auditorios',
        'equipment' => 'equipos',
        'equipment_event' => 'reserva_equipo',
        'event_user' => 'usuario_reserva',
    ];

    private array $camposReserva = [
        'title' => 'titulo', 'start_time' => 'fecha_inicio', 'end_time' => 'fecha_fin',
        'user_name' => 'nombre_usuario', 'user_phone' => 'telefono_usuario',
        'authority_name' => 'nombre_autoridad', 'responsible' => 'responsable',
        'event_type' => 'tipo_evento',
        'requirements' => 'requerimientos', 'capacity' => 'aforo', 'entry_type' => 'tipo_ingreso',
        'external_coordinator_name' => 'nombre_coordinador_externo',
        'external_coordinator_phone' => 'telefono_coordinador_externo',
        'special_requirements' => 'requerimientos_especiales', 'registered_by' => 'registrado_por',
        'internal_coordinator' => 'coordinador_interno', 'created_at' => 'fecha_creacion',
        'updated_at' => 'fecha_actualizacion', 'deleted_at' => 'eliminado_en',
    ];

    private array $camposUsuario = [
        'name' => 'nombre', 'email' => 'correo', 'phone' => 'telefono', 'role' => 'rol',
        'email_verified_at' => 'correo_verificado_en', 'password' => 'contrasena',
        'remember_token' => 'token_recordar', 'created_at' => 'fecha_creacion',
        'updated_at' => 'fecha_actualizacion',
    ];

    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasTable('reservas')) {
            Schema::rename('events', 'reservas');
        } elseif (Schema::hasTable('events') && Schema::hasTable('reservas')
            && !Schema::hasTable('eventos')) {
            // Si ya existen ambas, conserva las dos y traduce la tabla anterior.
            Schema::rename('events', 'eventos');
        }

        foreach ($this->tablas as $anterior => $nuevo) {
            if (Schema::hasTable($anterior) && !Schema::hasTable($nuevo)) {
                Schema::rename($anterior, $nuevo);
            }
        }

        foreach ($this->camposReserva as $anterior => $nuevo) {
            $this->renombrarColumna('reservas', $anterior, $nuevo);
            $this->renombrarColumna('eventos', $anterior, $nuevo);
        }

        foreach ($this->camposUsuario as $anterior => $nuevo) {
            $this->renombrarColumna('users', $anterior, $nuevo);
        }

        if (Schema::hasTable('users') && !Schema::hasTable('usuarios')) {
            Schema::rename('users', 'usuarios');
        }

        $this->renombrarColumna('auditorios', 'name', 'nombre');
        $this->renombrarColumna('auditorios', 'location', 'ubicacion');
        $this->renombrarColumna('equipos', 'name', 'nombre');
        $this->renombrarColumna('equipos', 'total_quantity', 'cantidad_total');
        $this->renombrarColumna('reserva_equipo', 'event_id', 'reserva_id');
        $this->renombrarColumna('reserva_equipo', 'equipment_id', 'equipo_id');
        $this->renombrarColumna('reserva_equipo', 'quantity_reserved', 'cantidad_reservada');
        $this->renombrarColumna('usuario_reserva', 'user_id', 'usuario_id');
        $this->renombrarColumna('usuario_reserva', 'event_id', 'reserva_id');

        if (DB::selectOne("SELECT to_regclass('events_id_seq') AS secuencia")?->secuencia
            && !DB::selectOne("SELECT to_regclass('reservas_id_seq') AS secuencia")?->secuencia) {
            DB::statement('ALTER SEQUENCE events_id_seq RENAME TO reservas_id_seq');
        }
        if (DB::selectOne("SELECT to_regclass('events_id_seq') AS secuencia")?->secuencia
            && Schema::hasTable('eventos')
            && !DB::selectOne("SELECT to_regclass('eventos_id_seq') AS secuencia")?->secuencia) {
            DB::statement('ALTER SEQUENCE events_id_seq RENAME TO eventos_id_seq');
        }        if (DB::selectOne("SELECT to_regclass('users_id_seq') AS secuencia")?->secuencia
            && !DB::selectOne("SELECT to_regclass('usuarios_id_seq') AS secuencia")?->secuencia) {
            DB::statement('ALTER SEQUENCE users_id_seq RENAME TO usuarios_id_seq');
        }

        $this->comentarCampos();
    }

    public function down(): void
    {
        foreach (array_reverse($this->camposReserva, true) as $anterior => $nuevo) {
            $this->renombrarColumna('reservas', $nuevo, $anterior);
        }
        foreach (array_reverse($this->camposUsuario, true) as $anterior => $nuevo) {
            $this->renombrarColumna('usuarios', $nuevo, $anterior);
        }
        if (Schema::hasTable('usuarios') && !Schema::hasTable('users')) {
            Schema::rename('usuarios', 'users');
        }
        foreach (array_reverse($this->tablas, true) as $anterior => $nuevo) {
            if (Schema::hasTable($nuevo) && !Schema::hasTable($anterior)) {
                Schema::rename($nuevo, $anterior);
            }
        }
        if (Schema::hasTable('reservas') && !Schema::hasTable('events')) {
            Schema::rename('reservas', 'events');
        }
    }

    private function renombrarColumna(string $tabla, string $anterior, string $nuevo): void
    {
        if (Schema::hasTable($tabla)
            && Schema::hasColumn($tabla, $anterior)
            && !Schema::hasColumn($tabla, $nuevo)) {
            Schema::table($tabla, function ($table) use ($anterior, $nuevo) {
                $table->renameColumn($anterior, $nuevo);
            });
        }
    }

    private function comentarCampos(): void
    {
        $campos = [
            'reservas' => [
                'titulo', 'fecha_inicio', 'fecha_fin', 'nombre_usuario', 'telefono_usuario',
                'nombre_autoridad', 'responsable', 'salon', 'tipo_evento', 'requerimientos',
                'aforo', 'tipo_ingreso', 'nombre_coordinador_externo',
                'telefono_coordinador_externo', 'requerimientos_especiales',
                'registrado_por', 'coordinador_interno', 'fecha_creacion',
                'fecha_actualizacion', 'eliminado_en',
            ],
            'usuarios' => [
                'nombre', 'correo', 'telefono', 'rol', 'correo_verificado_en',
                'contrasena', 'token_recordar', 'fecha_creacion', 'fecha_actualizacion',
            ],
        ];

        foreach ($campos as $tabla => $columnas) {
            foreach ($columnas as $columna) {
                if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, $columna)) {
                    DB::statement("COMMENT ON COLUMN {$tabla}.{$columna} IS 'Campo en espanol. Cambio 12/10/2026.'");
                }
            }
        }
    }
};
