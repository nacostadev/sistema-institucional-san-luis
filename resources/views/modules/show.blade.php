@extends('layouts.app')

@section('content')
@if (session('message'))
<div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
    <i class="bx bx-check-circle me-2 fs-5"></i>
    <div>{{ session('message') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
    <i class="bx bx-error-circle me-2 fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    @if ($canCreate)
    <div class="col-12 col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Nuevo registro</h5>
                <small class="text-muted">Agregar en {{ $title }}</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('modules.store', $module) }}">
                    @csrf
                    @foreach ($definition['fields'] as $field)
                    @if (!($field['hidden_create'] ?? false) && !in_array($field['name'], ['estado', 'estado_matricula'], true))
                    @php($type = $field['type'] ?? 'text')
                    <div class="mb-3">
                        @if ($type === 'checkbox')
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}">
                            <label class="form-check-label" for="new-{{ $field['name'] }}">{{ $field['label'] }}</label>
                        </div>
                        @else
                        <label class="form-label" for="new-{{ $field['name'] }}">{{ $field['label'] }}</label>
                        @if ($type === 'textarea')
                        <textarea class="form-control" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}" rows="2" placeholder="Ingresa {{ strtolower($field['label']) }}"></textarea>
                        @elseif ($type === 'select')
                        <select class="form-select" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}">
                            <option value="">Seleccionar {{ strtolower($field['label']) }}</option>
                            @foreach ($field['options'] as $option)
                            <option value="{{ $option }}">{{ ucfirst(str_replace('_', ' ', $option)) }}</option>
                            @endforeach
                        </select>
                        @elseif ($type === 'relation')
                        <select class="form-select" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}">
                            <option value="">Seleccionar {{ strtolower($field['label']) }}</option>
                            @foreach ($relations[$field['name']] as $option)
                            <option value="{{ $option->{$field['value']} }}">{{ $option->display_label ?? $option->{$field['display']} }}</option>
                            @endforeach
                        </select>
                        @elseif ($field['name'] === 'calificacion')
                        <input class="form-control" type="number" min="0" max="20" step="1" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}" placeholder="0 a 20">
                        @else
                        <input class="form-control" type="{{ $type }}" name="{{ $field['name'] }}" id="new-{{ $field['name'] }}" placeholder="Ingresa {{ strtolower($field['label']) }}">
                        @endif
                        @endif
                    </div>
                    @endif
                    @endforeach
                    <button class="btn btn-primary w-100 py-2 mt-2">
                        <i class="bx bx-plus me-1"></i>Crear {{ strtolower($title) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="col-12 {{ $canCreate ? 'col-xl-8' : 'col-xl-12' }}">
        <div class="card h-100">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $title }}</h5>
                    <small class="text-muted">{{ $subtitle ?? 'Listado disponible en el sistema' }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('modules.show', $module) }}" class="d-flex align-items-center gap-1">
                        @if(request('id_alumno'))
                        <input type="hidden" name="id_alumno" value="{{ request('id_alumno') }}">
                        @endif
                        @if(request('editar'))
                        <input type="hidden" name="editar" value="{{ request('editar') }}">
                        @endif
                        @if(request('edit'))
                        <input type="hidden" name="edit" value="{{ request('edit') }}">
                        @endif
                        <div class="input-group input-group-sm">
                            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar...">
                            <button type="submit" class="btn btn-outline-primary"><i class="bx bx-search"></i></button>
                        </div>
                    </form>
                    <span class="badge bg-label-primary text-nowrap">{{ $rows->count() }} registros</span>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ ucfirst($definition['key']) }}</th>
                            @foreach ($definition['fields'] as $field)
                            <th>{{ $field['label'] }}</th>
                            @endforeach
                            @if ($canEdit || $canDelete)
                            <th class="text-end">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                        <tr>
                            <td><span class="fw-semibold text-primary">#{{ $row->{$definition['key']} }}</span></td>
                            @foreach ($definition['fields'] as $field)
                            @php($type = $field['type'] ?? 'text')
                            <td>
                                @if ($type === 'relation')
                                @php($related = $relations[$field['name']]->firstWhere($field['value'], $row->{$field['name']}))
                                {{ $related?->display_label ?? $related?->{$field['display']} ?? $row->{$field['name']} }}
                                @elseif ($type === 'checkbox')
                                <span class="badge bg-label-{{ $row->{$field['name']} ? 'success' : 'secondary' }}">{{ $row->{$field['name']} ? 'Sí' : 'No' }}</span>
                                @elseif (str_contains($field['name'], 'estado'))
                                <span class="badge bg-label-{{ in_array($row->{$field['name']}, ['activo', 'matriculado', 'presente']) ? 'success' : 'warning' }}">{{ ucfirst($row->{$field['name']}) }}</span>
                                @elseif ($field['name'] === 'calificacion')
                                <span class="badge {{ (int)$row->{$field['name']} >= 11 ? 'bg-label-primary' : 'bg-label-danger' }} fs-6 fw-bold px-2 py-1">{{ (int)$row->{$field['name']} }}</span>
                                @elseif (in_array($field['name'], ['descripcion', 'tema', 'observaciones'], true))
                                {!! $row->{$field['name']} !!}
                                @else
                                {{ \Illuminate\Support\Str::limit((string) $row->{$field['name']}, 40) }}
                                @endif
                            </td>
                            @endforeach

                            @if ($canEdit || $canDelete)
                            <td class="text-end">
                                <div class="module-actions">
                                    @if ($canEdit)
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" title="Editar" data-bs-toggle="modal" data-bs-target="#edit-{{ $row->{$definition['key']} }}">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    @endif
                                    @if ($canDelete)
                                    <form method="POST" action="{{ route('modules.destroy', [$module, $row->{$definition['key']}]) }}" onsubmit="return confirm('¿Desea cambiar o eliminar este registro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Eliminar">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ count($definition['fields']) + ($canEdit || $canDelete ? 2 : 1) }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    <span>No se encontraron registros para mostrar.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if ($canEdit)
@foreach ($rows as $row)
<div class="modal fade" id="edit-{{ $row->{$definition['key']} }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('modules.update', [$module, $row->{$definition['key']}]) }}">
                @csrf
                @method('PUT')
                @if(request('id_alumno'))
                <input type="hidden" name="id_alumno" value="{{ request('id_alumno') }}">
                @endif
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Editar {{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($definition['fields'] as $field)
                        @php($type = $field['type'] ?? 'text')
                        <div class="col-md-6 mb-3">
                            @if ($type === 'checkbox')
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="{{ $field['name'] }}" id="edit-{{ $row->{$definition['key']} }}-{{ $field['name'] }}" {{ $row->{$field['name']} ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit-{{ $row->{$definition['key']} }}-{{ $field['name'] }}">{{ $field['label'] }}</label>
                            </div>
                            @else
                            <label class="form-label">{{ $field['label'] }}</label>
                            @if ($type === 'textarea')
                            <textarea class="form-control" name="{{ $field['name'] }}" rows="2">{{ $row->{$field['name']} }}</textarea>
                            @elseif ($type === 'select')
                            <select class="form-select" name="{{ $field['name'] }}">
                                @foreach ($field['options'] as $option)
                                <option value="{{ $option }}" {{ $row->{$field['name']} === $option ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>
                                @endforeach
                            </select>
                            @elseif ($type === 'relation')
                            <select class="form-select" name="{{ $field['name'] }}">
                                @foreach ($relations[$field['name']] as $option)
                                <option value="{{ $option->{$field['value']} }}" {{ $row->{$field['name']} == $option->{$field['value']} ? 'selected' : '' }}>{{ $option->display_label ?? $option->{$field['display']} }}</option>
                                @endforeach
                            </select>
                            @elseif ($field['name'] === 'calificacion')
                            <input class="form-control" type="number" min="0" max="20" step="1" name="{{ $field['name'] }}" value="{{ (int)$row->{$field['name']} }}" required>
                            @else
                            <input class="form-control" type="{{ $type }}" name="{{ $field['name'] }}" value="{{ $type === 'password' ? '' : $row->{$field['name']} }}">
                            @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection