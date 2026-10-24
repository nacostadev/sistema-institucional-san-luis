<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'nota';
    protected $primaryKey = 'id_nota';
    public $timestamps = false;
    protected $fillable = [
        'evaluacion_id',
        'matricula_id',
        'seccion_id',
        'calificacion',
        'registrada_por',
        'actualizada_en',
    ];

    protected function casts(): array
    {
        return [
            'calificacion' => 'decimal:2',
            'actualizada_en' => 'datetime',
        ];
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id', 'id_evaluacion');
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id', 'id_matricula');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id', 'id_seccion');
    }

    public function registradaPor()
    {
        return $this->belongsTo(User::class, 'registrada_por', 'id_usuario');
    }
}
