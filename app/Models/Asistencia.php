<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;
    protected $fillable = ['sesion_id', 'matricula_id', 'estado'];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id', 'id_matricula');
    }
}
