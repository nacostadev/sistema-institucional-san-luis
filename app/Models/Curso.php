<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'curso';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;
    protected $fillable = ['costo_id', 'nombre', 'descripcion', 'estado'];
    public function costo()
    {
        return $this->belongsTo(Costo::class, 'costo_id', 'id_costo');
    }
    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'curso_id', 'id_curso');
    }
}
