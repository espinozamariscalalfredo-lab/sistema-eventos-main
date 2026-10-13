<?php

namespace App\Database\Migraciones;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

/** Adapta el historial de migraciones a columnas en español. */
class RepositorioMigracionesEspanol extends DatabaseMigrationRepository
{
    public function getRan()
    {
        return $this->tabla()
            ->orderBy('lote', 'asc')
            ->orderBy('nombre_migracion', 'asc')
            ->pluck('nombre_migracion')
            ->all();
    }

    public function getMigrations($steps)
    {
        return $this->tabla()
            ->where('lote', '>=', 1)
            ->orderBy('lote', 'desc')
            ->orderBy('nombre_migracion', 'desc')
            ->limit($steps)
            ->get(['id_migracion as id', 'nombre_migracion as migration', 'lote as batch'])
            ->all();
    }

    public function getMigrationsByBatch($batch)
    {
        return $this->tabla()
            ->where('lote', $batch)
            ->orderBy('nombre_migracion', 'desc')
            ->get(['id_migracion as id', 'nombre_migracion as migration', 'lote as batch'])
            ->all();
    }

    public function getLast()
    {
        return $this->getMigrationsByBatch($this->getLastBatchNumber());
    }

    public function getMigrationBatches()
    {
        return $this->tabla()
            ->orderBy('lote', 'asc')
            ->orderBy('nombre_migracion', 'asc')
            ->pluck('lote', 'nombre_migracion')
            ->all();
    }

    public function log($file, $batch)
    {
        $this->tabla()->insert(['nombre_migracion' => $file, 'lote' => $batch]);
    }

    public function delete($migration)
    {
        $this->tabla()->where('nombre_migracion', $migration->migration)->delete();
    }

    public function getLastBatchNumber()
    {
        return (int) ($this->tabla()->max('lote') ?? 0);
    }

    public function createRepository()
    {
        $this->getConnection()->getSchemaBuilder()->create($this->table, function ($tabla) {
            $tabla->increments('id_migracion');
            $tabla->string('nombre_migracion');
            $tabla->integer('lote');
        });
    }

    protected function tabla()
    {
        return $this->getConnection()->table($this->table)->useWritePdo();
    }
}
