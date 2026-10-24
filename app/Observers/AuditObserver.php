<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    private const SENSITIVE_ATTRIBUTES = [
        'access_token',
        'apellidos_cifrados',
        'api_token',
        'correo_cifrado',
        'correo_hash',
        'dni_cifrado',
        'dni_hash',
        'nombres_cifrados',
        'password',
        'password_confirmation',
        'telefono_cifrado',
        'refresh_token',
        'remember_token',
    ];

    public function created(Model $model): void
    {
        $this->record($model, 'INSERT', null, $this->snapshot($model));
    }

    public function updated(Model $model): void
    {
        $this->record(
            $model,
            'UPDATE',
            $this->filterSensitiveAttributes($model->getRawOriginal()),
            $this->snapshot($model)
        );
    }

    private function snapshot(Model $model): array
    {
        return $this->filterSensitiveAttributes($model->getAttributes());
    }

    private function filterSensitiveAttributes(array $attributes): array
    {
        return array_filter(
            $attributes,
            static fn (string $attribute): bool => ! in_array(
                strtolower($attribute),
                self::SENSITIVE_ATTRIBUTES,
                true
            ),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function record(Model $model, string $action, ?array $before, ?array $after): void
    {
        $model->getConnection()->table('audit_logs')->insert([
            'creado_en' => now(),
            'actor_usuario_id' => Auth::id(),
            'tabla' => $model->getTable(),
            'registro_id' => $model->getKey(),
            'accion' => $action,
            'valores_anteriores' => $before === null ? null : json_encode(
                $before,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            'valores_nuevos' => $after === null ? null : json_encode(
                $after,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            'ip_origen' => request()->ip(),
        ]);
    }
}
