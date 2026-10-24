<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodo';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;

    protected $fillable = ['codigo', 'fecha_inicio', 'fecha_fin', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'periodo_id', 'id_periodo');
    }

    public function cargos()
    {
        return $this->hasMany(CargoAdicional::class, 'periodo_id', 'id_periodo');
    }
}
