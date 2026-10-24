<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;
    protected $fillable = ['codigo', 'descripcion', 'estado'];

    protected function getNombreRolAttribute(): string
    {
        return match (strtoupper((string) $this->codigo)) {
            'ADMIN', 'ADMINISTRADOR' => 'Administrador',
            'COORDINADOR' => 'Coordinador',
            'DOCENTE' => 'Docente',
            'ALUMNO', 'ESTUDIANTE' => 'Alumno',
            default => $this->descripcion ?: (string) $this->codigo,
        };
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_rol', 'rol_id', 'usuario_id')
            ->wherePivot('estado', 'activo')
            ->withPivot(['id_usuario_rol', 'asignado_en', 'estado']);
    }
}
