<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $table = 'programa';
    protected $primaryKey = 'id_programa';
    public $timestamps = false;

    protected $fillable = ['nombre', 'estado'];

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'programa_id', 'id_programa');
    }
}
