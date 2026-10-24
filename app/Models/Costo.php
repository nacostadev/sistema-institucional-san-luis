<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Costo extends Model
{
    protected $table = 'costo';
    protected $primaryKey = 'id_costo';
    public $timestamps = true;
    public const CREATED_AT = null;
    public const UPDATED_AT = 'updated_at';
    protected $fillable = ['nombre_concepto', 'categoria', 'monto', 'estado', 'updated_at'];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'updated_at' => 'datetime',
        ];
    }
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'costo_id', 'id_costo');
    }
    public function cargos()
    {
        return $this->hasMany(CargoAdicional::class, 'costo_id', 'id_costo');
    }
}
