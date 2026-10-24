<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionClase extends Model
{
    protected $table = 'sesion_clase';
    protected $primaryKey = 'id_sesion';
    public $timestamps = false;
    protected $fillable = ['seccion_id', 'fecha', 'tema'];
    protected $casts = ['fecha' => 'date'];
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id', 'id_seccion');
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'sesion_id', 'id_sesion');
    }
}
