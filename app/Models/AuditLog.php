<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'creado_en',
        'actor_usuario_id',
        'ip_origen',
        'tabla',
        'registro_id',
        'accion',
        'valores_anteriores',
        'valores_nuevos',
    ];

    protected function casts(): array
    {
        return [
            'creado_en' => 'datetime',
            'valores_anteriores' => 'array',
            'valores_nuevos' => 'array',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_usuario_id', 'id_usuario');
    }
}
