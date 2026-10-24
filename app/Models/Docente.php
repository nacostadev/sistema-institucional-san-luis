<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;
    protected $fillable = ['usuario_id', 'dni', 'nombre_completo', 'especialidad', 'telefono', 'estado'];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'docente_id', 'id_docente');
    }
}
