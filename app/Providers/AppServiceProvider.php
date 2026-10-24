<?php

namespace App\Providers;

use App\Models\Asistencia;
use App\Models\Aula;
use App\Models\Costo;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\HorarioProgramacion;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Pago;
use App\Models\Rol;
use App\Models\RolVistaPermiso;
use App\Models\Seccion;
use App\Models\SesionClase;
use App\Models\User;
use App\Models\Vista;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([
            Asistencia::class,
            Aula::class,
            Costo::class,
            Curso::class,
            Docente::class,
            Estudiante::class,
            Evaluacion::class,
            HorarioProgramacion::class,
            Matricula::class,
            Nota::class,
            Pago::class,
            Rol::class,
            RolVistaPermiso::class,
            Seccion::class,
            SesionClase::class,
            User::class,
            Vista::class,
        ] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
