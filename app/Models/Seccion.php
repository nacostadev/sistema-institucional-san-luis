<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'seccion';
    protected $primaryKey = 'id_seccion';
    public $timestamps = false;
    protected $fillable = ['curso_id', 'periodo_id', 'docente_id', 'nombre', 'cupo_maximo', 'estado'];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id', 'id_periodo');
    }
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
        return $this->hasMany(Horario::class, 'seccion_id', 'id_seccion');
    }
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'seccion_id', 'id_seccion');
    }
    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'seccion_id', 'id_seccion');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'seccion_id', 'id_seccion');
    }
}
