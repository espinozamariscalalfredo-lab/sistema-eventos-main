<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Documenta el esquema de PostgreSQL. Cambio: 12/10/2026.
return new class extends Migration
{
    private array $tablas = [
        'reservas' => [
            'texto' => 'Reservas activas del calendario. Se conserva el historial mediante baja lógica.',
            'columnas' => [
                'titulo' => 'Título o asunto de la reserva.',
                'fecha_inicio' => 'Fecha y hora en que inicia la reserva.',
                'fecha_fin' => 'Fecha y hora en que termina la reserva.',
                'nombre_usuario' => 'Nombre de la persona que solicita la reserva.',
                'telefono_usuario' => 'Teléfono de contacto de quien solicita.',
                'nombre_autoridad' => 'Autoridad asociada al evento.',
                'responsable' => 'Persona responsable del evento.',
                'salon' => 'Nombre del salón reservado; coincide con salones.nombre.',
                'tipo_evento' => 'Categoría del evento.',
                'requerimientos' => 'Lista de requerimientos del evento.',
                'aforo' => 'Cantidad estimada de asistentes.',
                'tipo_ingreso' => 'Modalidad de ingreso de los asistentes.',
                'nombre_coordinador_externo' => 'Nombre del coordinador externo.',
                'telefono_coordinador_externo' => 'Teléfono del coordinador externo.',
                'requerimientos_especiales' => 'Detalle de necesidades especiales.',
                'registrado_por' => 'Correo o nombre de quien registró la reserva.',
                'coordinador_interno' => 'Coordinador interno asignado.',
                'modificado_por' => 'Usuario que hizo la última modificación.',
                'fecha_creacion' => 'Fecha en que se creó el registro.',
                'fecha_actualizacion' => 'Fecha del último cambio al registro.',
                'eliminado_en' => 'Fecha de baja lógica; NULL significa que sigue activa.',
            ],
        ],
        'salones' => [
            'texto' => 'Catálogo de salones y colores que usa el calendario.',
            'columnas' => [
                'nombre' => 'Nombre que se muestra en filtros y reservas.',
                'color' => 'Color hexadecimal del salón en el calendario.',
                'activo' => 'Indica si el salón se ofrece para nuevas reservas.',
                'orden' => 'Posición del salón en la leyenda y el calendario.',
            ],
        ],
        'usuarios' => [
            'texto' => 'Cuentas que pueden ingresar al sistema.',
            'columnas' => [
                'nombre' => 'Nombre visible de la cuenta.',
                'correo' => 'Correo usado para iniciar sesión y enviar avisos.',
                'rol' => 'Permisos asignados a la cuenta.',
                'contrasena' => 'Contraseña guardada como hash; nunca guardar texto plano.',
            ],
        ],
        'eventos' => [
            'texto' => 'Tabla histórica conservada; la agenda actual usa reservas.',
            'columnas' => [],
        ],
        'tokens_restablecimiento' => [
            'texto' => 'Tokens temporales para restablecer contraseñas.',
            'columnas' => [],
        ],
        'historial_migraciones' => [
            'texto' => 'Registro de migraciones aplicadas por Laravel.',
            'columnas' => [],
        ],
        'sesiones' => [
            'texto' => 'Sesiones guardadas en PostgreSQL cuando se activa ese controlador.',
            'columnas' => [],
        ],
        'almacenamiento_cache' => [
            'texto' => 'Datos de caché guardados en PostgreSQL cuando se activa ese controlador.',
            'columnas' => [],
        ],
        'bloqueos_cache' => [
            'texto' => 'Bloqueos de caché usados por Laravel.',
            'columnas' => [],
        ],
        'trabajos' => [
            'texto' => 'Cola de tareas de Laravel; se conserva aunque la cola actual sea síncrona.',
            'columnas' => [],
        ],
        'trabajos_fallidos' => [
            'texto' => 'Registro de tareas de Laravel que fallaron.',
            'columnas' => [],
        ],
        'lotes_trabajos' => [
            'texto' => 'Grupos de tareas procesados por Laravel.',
            'columnas' => [],
        ],
    ];

    public function up(): void
    {
        foreach ($this->tablas as $nombre => $detalle) {
            if (! Schema::hasTable($nombre)) {
                continue;
            }

            DB::statement('COMMENT ON TABLE "'.$nombre.'" IS '.DB::getPdo()->quote($detalle['texto']));

            foreach ($detalle['columnas'] as $columna => $texto) {
                if (Schema::hasColumn($nombre, $columna)) {
                    DB::statement('COMMENT ON COLUMN "'.$nombre.'"."'.$columna.'" IS '.DB::getPdo()->quote($texto.' Cambio 12/10/2026.'));
                }
            }
        }
    }

    public function down(): void
    {
        // Se conservan los comentarios porque documentan el esquema existente.
    }
};
