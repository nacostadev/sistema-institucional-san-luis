<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiante';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;
    protected $fillable = ['usuario_id', 'codigo_estudiante', 'estado'];
    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->usuario?->nombres,
            $this->usuario?->apellidos,
        ])));
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'estudiante_id', 'id_estudiante');
    }
}
