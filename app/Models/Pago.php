<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;
    protected $fillable = [
        'matricula_id',
        'cargo_id',
        'monto',
        'serie_comprobante',
        'numero_comprobante',
        'evidencia_ref',
        'fecha_pago',
        'estado',
        'registrado_por',
        'validado_por',
        'validado_en',
        'motivo_anulacion',
        'creado_en',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'datetime',
            'validado_en' => 'datetime',
            'creado_en' => 'datetime',
        ];
    }
    public function matricula()
    {
        return $this->belongsTo(Matricula::class, 'matricula_id', 'id_matricula');
    }
    public function cargo()
    {
        return $this->belongsTo(CargoAdicional::class, 'cargo_id', 'id_cargo');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por', 'id_usuario');
    }

    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'validado_por', 'id_usuario');
    }
}
