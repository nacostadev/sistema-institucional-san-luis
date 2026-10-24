<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    protected $table = 'estudiante';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;
    protected $fillable = ['usuario_id', 'dni', 'nombre_completo', 'telefono', 'estado'];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'estudiante_id', 'id_estudiante');
    }
}
