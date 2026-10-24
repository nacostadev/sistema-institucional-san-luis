<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    private const SENSITIVE_ATTRIBUTES = [
        'access_token',
        'api_token',
        'clave',
        'password',
        'password_confirmation',
        'refresh_token',
        'remember_token',
    ];

    public function creating(Model $model): void
    {
        $model->setAttribute('created_by', Auth::id());
        $model->setAttribute('updated_by', Auth::id());
    }

    public function updating(Model $model): void
    {
        $model->setAttribute('updated_by', Auth::id());
    }

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

    public function deleted(Model $model): void
    {
        $this->record($model, 'DELETE', $this->snapshot($model), null);
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
            'usuario_id' => Auth::id(),
            'tabla_afectada' => $model->getTable(),
            'registro_id' => $model->getKey(),
            'accion' => $action,
            'datos_anteriores' => $before === null ? null : json_encode(
                $before,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            'datos_nuevos' => $after === null ? null : json_encode(
                $after,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            'ip_origen' => request()->ip(),
        ]);
    }
}
