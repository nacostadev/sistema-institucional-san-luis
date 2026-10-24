<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;
    protected $fillable = ['nombre_rol', 'descripcion', 'estado'];
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id_rol');
    }
    public function permisos()
    {
        return $this->hasMany(RolVistaPermiso::class, 'rol_id', 'id_rol');
    }
}
