<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $table = 'matricula';
    protected $primaryKey = 'id_matricula';
    public $timestamps = false;
    protected $fillable = ['estudiante_id', 'seccion_id', 'fecha_matricula', 'estado_matricula'];
    protected $casts = ['fecha_matricula' => 'date'];
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id', 'id_seccion');
    }
    public function notas()
    {
        return $this->hasMany(Nota::class, 'matricula_id', 'id_matricula');
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'matricula_id', 'id_matricula');
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'matricula_id', 'id_matricula');
    }
}
