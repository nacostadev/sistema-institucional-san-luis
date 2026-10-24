<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargoAdicional extends Model
{
    protected $table = 'cargo_adicional';
    protected $primaryKey = 'id_cargo';
    public $timestamps = false;

    protected $fillable = [
        'estudiante_id',
        'periodo_id',
        'costo_id',
        'referencia_interna',
        'monto_aplicado',
        'fecha_cargo',
        'fecha_vencimiento',
        'estado',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'monto_aplicado' => 'decimal:2',
            'fecha_cargo' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'id_estudiante');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id', 'id_periodo');
    }

    public function costo()
    {
        return $this->belongsTo(Costo::class, 'costo_id', 'id_costo');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cargo_id', 'id_cargo');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por', 'id_usuario');
    }
}
