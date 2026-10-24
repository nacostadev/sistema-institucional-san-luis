<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Costo extends Model
{
    protected $table = 'costos';
    protected $primaryKey = 'id_costo';
    public $timestamps = false;
    protected $fillable = ['nombre_costo', 'monto', 'descripcion', 'estado'];
    protected $casts = ['monto' => 'decimal:2'];
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'costo_id', 'id_costo');
    }
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'costo_id', 'id_costo');
    }
}
