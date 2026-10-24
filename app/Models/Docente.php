<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;
    protected $fillable = ['usuario_id', 'especialidad', 'estado'];
    protected $appends = ['nombre_completo'];

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->usuario?->nombres,
            $this->usuario?->apellidos,
        ])));
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'docente_id', 'id_docente');
    }

    public function habilitaciones()
    {
        return $this->hasMany(DocenteHabilitacion::class, 'docente_id', 'id_docente');
    }
}
