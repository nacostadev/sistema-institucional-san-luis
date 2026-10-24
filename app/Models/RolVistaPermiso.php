<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolVistaPermiso extends Model
{
    protected $table = 'rol_vista_permiso';
    protected $primaryKey = 'id_permiso';
    public $timestamps = false;
    protected $fillable = ['rol_id', 'vista_id', 'puede_crear', 'puede_editar', 'puede_eliminar'];
    protected $casts = ['puede_crear' => 'boolean', 'puede_editar' => 'boolean', 'puede_eliminar' => 'boolean'];
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id_rol');
    }
    public function vista()
    {
        return $this->belongsTo(Vista::class, 'vista_id', 'id_vista');
    }
}
