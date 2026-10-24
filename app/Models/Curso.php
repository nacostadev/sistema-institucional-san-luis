<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'curso';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;
    protected $fillable = ['programa_id', 'costo_id', 'nombre', 'descripcion', 'estado'];

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id', 'id_programa');
    }
    public function costo()
    {
        return $this->belongsTo(Costo::class, 'costo_id', 'id_costo');
    }
    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'curso_id', 'id_curso');
    }

    public function habilitaciones()
    {
        return $this->hasMany(DocenteHabilitacion::class, 'curso_id', 'id_curso');
    }
}
