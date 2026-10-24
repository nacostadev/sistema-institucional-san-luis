<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluacion';
    protected $primaryKey = 'id_evaluacion';
    public $timestamps = false;
    protected $fillable = ['seccion_id', 'titulo', 'ponderacion', 'fecha_evaluacion', 'estado'];

    protected function casts(): array
    {
        return [
            'ponderacion' => 'decimal:2',
            'fecha_evaluacion' => 'date',
        ];
    }
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id', 'id_seccion');
    }
    public function notas()
    {
        return $this->hasMany(Nota::class, 'evaluacion_id', 'id_evaluacion');
    }
}
