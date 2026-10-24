<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioProgramacion extends Model
{
    protected $table = 'horario_programacion';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;
    protected $fillable = ['seccion_id', 'aula_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id', 'id_seccion');
    }
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id', 'id_aula');
    }
}
