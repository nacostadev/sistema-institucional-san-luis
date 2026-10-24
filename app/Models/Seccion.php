<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'seccion';
    protected $primaryKey = 'id_seccion';
    public $timestamps = false;
    protected $fillable = ['curso_id', 'docente_id', 'nombre_seccion', 'periodo_lectivo', 'estado'];
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id_curso');
    }
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id', 'id_docente');
    }
    public function horarios()
    {
        return $this->hasMany(HorarioProgramacion::class, 'seccion_id', 'id_seccion');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'seccion_id', 'id_seccion');
    }
    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'seccion_id', 'id_seccion');
    }
}
