<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'nota';
    protected $primaryKey = 'id_nota';
    public $timestamps = false;
    protected $fillable = ['evaluacion_id', 'matricula_id', 'calificacion'];
    protected $casts = ['calificacion' => 'integer', 'fecha_registro' => 'datetime'];

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id', 'id_evaluacion');
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id', 'id_matricula');
    }
}
