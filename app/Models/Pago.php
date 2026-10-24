<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;
    protected $fillable = ['matricula_id', 'costo_id', 'monto_pagado', 'fecha_pago', 'metodo_pago', 'nro_comprobante'];
    protected $casts = ['monto_pagado' => 'decimal:2', 'fecha_pago' => 'datetime'];
    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id', 'id_matricula');
    }
    public function costo()
    {
        return $this->belongsTo(Costo::class, 'costo_id', 'id_costo');
    }
}
