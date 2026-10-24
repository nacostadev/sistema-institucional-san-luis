<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vista extends Model
{
    protected $table = 'vista';
    protected $primaryKey = 'id_vista';
    public $timestamps = false;
    protected $fillable = ['nombre_vista', 'ruta_url', 'icono', 'modulo_padre', 'estado'];
    public function permisos()
    {
        return $this->hasMany(RolVistaPermiso::class, 'vista_id', 'id_vista');
    }
}
