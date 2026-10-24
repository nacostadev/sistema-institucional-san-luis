<?php

namespace App\Models;

use App\Services\IdentidadService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'nombre_usuario',
        'dni',
        'correo',
        'nombres',
        'apellidos',
        'telefono',
        'password',
        'estado',
    ];

    protected $hidden = [
        'dni_cifrado',
        'dni_hash',
        'correo_cifrado',
        'correo_hash',
        'nombres_cifrados',
        'apellidos_cifrados',
        'telefono_cifrado',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getDniAttribute(): ?string
    {
        return $this->decryptAttribute('dni_cifrado');
    }

    public function setDniAttribute(?string $dni): void
    {
        $this->setEncryptedPii('dni_cifrado', 'dni_hash', $dni, 'dni');
    }

    public function getCorreoAttribute(): ?string
    {
        return $this->decryptAttribute('correo_cifrado');
    }

    public function setCorreoAttribute(?string $correo): void
    {
        $this->setEncryptedPii('correo_cifrado', 'correo_hash', $correo, 'correo');
    }

    public function getNombresAttribute(): ?string
    {
        return $this->decryptAttribute('nombres_cifrados');
    }

    public function setNombresAttribute(?string $nombres): void
    {
        $this->attributes['nombres_cifrados'] = $nombres === null
            ? null
            : app(IdentidadService::class)->cifrar($nombres);
    }

    public function getApellidosAttribute(): ?string
    {
        return $this->decryptAttribute('apellidos_cifrados');
    }

    public function setApellidosAttribute(?string $apellidos): void
    {
        $this->attributes['apellidos_cifrados'] = $apellidos === null
            ? null
            : app(IdentidadService::class)->cifrar($apellidos);
    }

    public function getTelefonoAttribute(): ?string
    {
        return $this->decryptAttribute('telefono_cifrado');
    }

    public function setTelefonoAttribute(?string $telefono): void
    {
        $this->attributes['telefono_cifrado'] = $telefono === null
            ? null
            : app(IdentidadService::class)->cifrar($telefono);
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password;
    }

    public function getEmailForPasswordReset(): ?string
    {
        return $this->correo;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'usuario_id', 'rol_id')
            ->wherePivot('estado', 'activo')
            ->withPivot(['id_usuario_rol', 'asignado_en', 'estado'])
            ->orderBy('usuario_rol.asignado_en');
    }

    public function getRolAttribute(): ?Rol
    {
        return $this->roles->first();
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'usuario_id', 'id_usuario');
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'usuario_id', 'id_usuario');
    }

    private function decryptAttribute(string $attribute): ?string
    {
        $value = $this->attributes[$attribute] ?? null;

        return $value === null ? null : app(IdentidadService::class)->descifrar($value);
    }

    private function setEncryptedPii(string $encryptedColumn, string $hashColumn, ?string $value, string $kind): void
    {
        if ($value === null) {
            $this->attributes[$encryptedColumn] = null;
            $this->attributes[$hashColumn] = null;

            return;
        }

        $identity = app(IdentidadService::class);
        $normalized = $kind === 'correo' ? strtolower($value) : $value;
        $this->attributes[$encryptedColumn] = $identity->cifrar($normalized);
        $this->attributes[$hashColumn] = $kind === 'correo'
            ? $identity->hashCorreo($normalized)
            : $identity->hashDni($normalized);
    }
}
