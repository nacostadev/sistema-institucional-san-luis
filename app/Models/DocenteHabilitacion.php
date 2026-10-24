<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteHabilitacion extends Model
{
    protected $table = 'docente_habilitacion';
    protected $primaryKey = 'id_habilitacion';
    public $timestamps = false;

    protected $fillable = ['docente_id', 'curso_id', 'habilitado'];

    protected function casts(): array
    {
        return ['habilitado' => 'boolean'];
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'docente_id', 'id_docente');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id_curso');
    }
}
