<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    protected $table = 'usuario_rol';
    protected $primaryKey = 'id_usuario_rol';
    public $timestamps = false;

    protected $fillable = ['usuario_id', 'rol_id', 'asignado_en', 'estado'];

    protected function casts(): array
    {
        return ['asignado_en' => 'datetime'];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'id_rol');
    }
}
