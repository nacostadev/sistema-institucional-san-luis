@extends('layouts.app', ['title' => 'Resumen'])

@section('content')
<div class="row">
    @foreach ($cards as [$key, $label, $icon, $color, $url])
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <a href="{{ $url }}" class="text-decoration-none">
            <div class="card card-interactive h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-label-{{ $color }} rounded p-2 mb-2">
                                <i class="bx {{ $icon }} bx-sm"></i>
                            </span>
                            <h6 class="card-title text-muted text-nowrap mt-2 mb-1">{{ $label }}</h6>
                            <h3 class="fw-bold mb-0 text-body">{{ $counts[$key] ?? 0 }}</h3>
                        </div>
                        <span class="text-muted"><i class="bx bx-chevron-right fs-4"></i></span>
                    </div>
                    <div class="mt-3 pt-1 border-top border-clean">
                        <small class="text-primary fw-semibold d-inline-flex align-items-center">
                            Ver detalles <i class="bx bx-right-arrow-alt ms-1"></i>
                        </small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach

    <div class="col-lg-8 col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold">Ruta de trabajo institucional</h5>
                    <small class="text-muted">Procesos clave de gestión</small>
                </div>
                <span class="badge bg-label-primary">Instituto San Luis</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded border border-clean h-100">
                            <span class="badge bg-label-primary mb-2">01</span>
                            <h6 class="fw-bold mt-1">Planifica</h6>
                            <p class="text-muted small mb-0">Cursos, secciones, aulas y horarios en un solo flujo organizado.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded border border-clean h-100">
                            <span class="badge bg-label-info mb-2">02</span>
                            <h6 class="fw-bold mt-1">Acompaña</h6>
                            <p class="text-muted small mb-0">Evaluaciones, notas y asistencia conectadas a cada matrícula.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded border border-clean h-100">
                            <span class="badge bg-label-success mb-2">03</span>
                            <h6 class="fw-bold mt-1">Administra</h6>
                            <p class="text-muted small mb-0">Personas, costos, pagos y permisos con trazabilidad completa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12 mb-4">
        <div class="card dashboard-welcome h-100">
            <div class="card-body d-flex flex-column justify-content-between position-relative">
                <div>
                    <span class="badge bg-white text-primary fw-bold mb-3">Rol: {{ $role }}</span>
                    <h5 class="text-white fw-bold">{{ $welcomeMsg }}</h5>
                    <p class="text-white-50 small mb-4">{{ $welcomeDesc }}</p>
                </div>
                <div>
                    <a class="btn btn-light btn-sm" href="{{ route('modules.show', 'cursos') }}">
                        <i class="bx bx-book-open me-1"></i> Ir a mis cursos
                    </a>
                </div>
                <i class="bx bx-book-open dashboard-illustration"></i>
            </div>
        </div>
    </div>
</div>
@endsection