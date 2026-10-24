<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;
    protected $fillable = ['nombre', 'tipo', 'capacidad', 'estado'];
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'aula_id', 'id_aula');
    }
}
