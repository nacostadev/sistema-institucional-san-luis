<!doctype html>
<html lang="es" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets') . '/' }}" dir="ltr" data-bs-theme="light" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Panel' }} | Instituto San Luis</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-san-luis-mark.svg') }}">
    <link rel="alternate icon" href="{{ asset('images/logo-san-luis-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script>
        (function() {
            try {
                const storedTheme = localStorage.getItem('sanluis-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', storedTheme);
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    @php
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $currentRole = $currentUser?->rol?->nombre_rol ?? 'Invitado';

        if ($currentRole === 'Docente') {
            $menuSections = [
                'Mis Clases' => [
                    ['cursos', 'Mis Cursos', 'bx-book'],
                    ['secciones', 'Mis Secciones', 'bx-category'],
                    ['horarios', 'Mis Horarios', 'bx-calendar'],
                    ['estudiantes', 'Mis Estudiantes', 'bx-group'],
                ],
                'Seguimiento Académico' => [
                    ['evaluaciones', 'Evaluaciones', 'bx-edit'],
                    ['notas', 'Calificaciones', 'bx-bar-chart-alt-2'],
                    ['sesiones', 'Sesiones de Clase', 'bx-time'],
                    ['asistencia', 'Control Asistencia', 'bx-check-square'],
                ],
            ];
        } elseif ($currentRole === 'Alumno') {
            $menuSections = [
                'Mi Actividad' => [
                    ['cursos', 'Mis Cursos', 'bx-book'],
                    ['horarios', 'Mi Horario', 'bx-calendar'],
                    ['matriculas', 'Mis Matrículas', 'bx-clipboard'],
                ],
                'Mi Rendimiento' => [
                    ['evaluaciones', 'Mis Evaluaciones', 'bx-edit'],
                    ['notas', 'Mis Calificaciones', 'bx-bar-chart-alt-2'],
                    ['sesiones', 'Sesiones de Clase', 'bx-time'],
                    ['asistencia', 'Mi Asistencia', 'bx-check-square'],
                ],
            ];
        } elseif ($currentRole === 'Coordinador') {
            $menuSections = [
                'Gestión Académica' => [
                    ['cursos', 'Cursos', 'bx-book'],
                    ['secciones', 'Secciones', 'bx-category'],
                    ['aulas', 'Aulas', 'bx-building'],
                    ['horarios', 'Horarios', 'bx-calendar'],
                    ['matriculas', 'Matrículas', 'bx-clipboard'],
                ],
                'Seguimiento' => [
                    ['evaluaciones', 'Evaluaciones', 'bx-edit'],
                    ['notas', 'Notas', 'bx-bar-chart-alt-2'],
                    ['sesiones', 'Sesiones', 'bx-time'],
                    ['asistencia', 'Asistencia', 'bx-check-square'],
                ],
                'Comunidad' => [
                    ['estudiantes', 'Estudiantes', 'bx-group'],
                    ['docentes', 'Docentes', 'bx-user-voice'],
                ],
            ];
        } else {
            // Administrador
            $menuSections = [
                'Gestión Académica' => [
                    ['cursos', 'Cursos', 'bx-book'],
                    ['secciones', 'Secciones', 'bx-category'],
                    ['aulas', 'Aulas', 'bx-building'],
                    ['horarios', 'Horarios', 'bx-calendar'],
                    ['matriculas', 'Matrículas', 'bx-clipboard'],
                ],
                'Seguimiento' => [
                    ['evaluaciones', 'Evaluaciones', 'bx-edit'],
                    ['notas', 'Notas', 'bx-bar-chart-alt-2'],
                    ['sesiones', 'Sesiones', 'bx-time'],
                    ['asistencia', 'Asistencia', 'bx-check-square'],
                ],
                'Administración' => [
                    ['estudiantes', 'Estudiantes', 'bx-group'],
                    ['docentes', 'Docentes', 'bx-user-voice'],
                    ['pagos', 'Pagos', 'bx-wallet'],
                    ['costos', 'Costos', 'bx-dollar-circle'],
                    ['usuarios', 'Usuarios', 'bx-user'],
                    ['roles', 'Roles y permisos', 'bx-lock-alt'],
                ],
            ];
        }
    @endphp

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu">
                <div class="app-brand demo">
                    <a href="{{ route('dashboard') }}" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bold ms-2 academic-brand"><img src="{{ asset('images/logo-san-luis.svg') }}" alt="Instituto San Luis" class="brand-logo"></span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i class="bx bx-chevron-left bx-sm"></i></a>
                </div>
                <div class="menu-divider mt-0"></div>
                <!-- <div class="menu-inner-shadow"></div> -->
                <ul class="menu-inner py-1">
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>Resumen</div>
                        </a>
                    </li>
                    @foreach ($menuSections as $sectionTitle => $items)
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">{{ $sectionTitle }}</span></li>
                    @foreach ($items as [$slug, $label, $icon])
                    <li class="menu-item {{ request()->route('module') === $slug ? 'active' : '' }}">
                        <a href="{{ route('modules.show', $slug) }}" class="menu-link">
                            <i class="menu-icon tf-icons bx {{ $icon }}"></i>
                            <div>{{ $label }}</div>
                        </a>
                    </li>
                    @endforeach
                    @endforeach
                </ul>
            </aside>
            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none"><a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)"><i class="bx bx-menu bx-sm"></i></a></div>
                    <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <span class="nav-item nav-link px-0"><i class="bx bx-search bx-sm"></i></span>
                            <span class="ms-2 d-none d-md-inline text-muted">Buscar en Instituto San Luis...</span>
                        </div>
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item me-3">
                                <button type="button" class="theme-switch-pill" data-theme-toggle title="Cambiar a modo oscuro" aria-label="Cambiar tema">
                                    <i class="bx bx-moon theme-icon"></i>
                                    <span class="theme-text d-none d-sm-inline">Modo oscuro</span>
                                </button>
                            </li>
                            <li class="nav-item navbar-dropdown dropdown-user dropdown"><a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online"><span class="avatar-initial rounded-circle">{{ strtoupper(substr($currentUser->nombre_usuario ?? 'SL', 0, 2)) }}</span></div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><span class="dropdown-item-text"><strong>{{ $currentUser->nombre_usuario ?? 'Usuario' }}</strong><br><small class="text-muted">{{ $currentUser->rol?->nombre_rol ?? 'Institucional' }}</small></span></li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger"><i class="bx bx-log-out me-2"></i><span class="align-middle">Cerrar sesión</span></button></form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h4 class="fw-bold py-3 mb-0">{{ $title ?? 'Panel principal' }}</h4>
                                <p class="text-muted mb-0">{{ $subtitle ?? 'Gestión académica, seguimiento y servicios estudiantiles' }}</p>
                            </div>
                        </div>
                        @yield('content')
                    </div>
                    <footer class="content-footer footer">
                        <div class="container-xxl d-flex flex-wrap justify-content-between align-items-center py-2">
                            <span>© {{ date('Y') }} Instituto San Luis · Sistema Institucional de Gestión Académica</span>
                            <span class="text-muted small">Versión 1.0</span>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
</body>

</html>
