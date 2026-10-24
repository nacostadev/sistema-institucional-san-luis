<!doctype html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión | Instituto San Luis</title>
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

<body class="auth-page">
    <div class="position-absolute top-0 end-0 p-3 p-md-4">
        <button type="button" class="theme-switch-pill" data-theme-toggle title="Cambiar a modo oscuro" aria-label="Cambiar tema">
            <i class="bx bx-moon theme-icon"></i>
            <span class="theme-text d-none d-sm-inline">Modo oscuro</span>
        </button>
    </div>

    <main class="login-shell">
        <section class="login-panel">
            <div class="login-brand">
                <img src="{{ asset('images/logo-san-luis.svg') }}" alt="Instituto San Luis" class="brand-logo mx-auto">
            </div>
            <div class="login-heading">
                <h1 class="fw-bold">Sistema Institucional</h1>
                <p class="text-muted">Ingresa tus credenciales para acceder al sistema.</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger py-2 d-flex align-items-center mb-3">
                <i class="bx bx-error-circle me-2 fs-5"></i>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="correo">Correo electrónico institucional</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                        <input class="form-control" type="email" id="correo" name="correo" value="{{ old('correo') }}" placeholder="usuario@instituto.edu.pe" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="clave">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                        <input class="form-control" type="password" id="clave" name="clave" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bx bx-log-in-circle me-1"></i> Iniciar sesión
                </button>
            </form>

            <p class="login-footer text-center mt-4 mb-0 small">
                © {{ date('Y') }} Instituto San Luis · Sistema Institucional
            </p>
        </section>
    </main>
</body>

</html>
