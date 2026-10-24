<?php

namespace App\Providers;

use App\Models\Aula;
use App\Models\CargoAdicional;
use App\Models\Costo;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\DocenteHabilitacion;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Horario;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Programa;
use App\Models\Rol;
use App\Models\Seccion;
use App\Models\User;
use App\Models\UsuarioRol;
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
            Aula::class,
            CargoAdicional::class,
            Costo::class,
            Curso::class,
            Docente::class,
            DocenteHabilitacion::class,
            Estudiante::class,
            Evaluacion::class,
            Horario::class,
            Matricula::class,
            Nota::class,
            Pago::class,
            Periodo::class,
            Programa::class,
            Rol::class,
            Seccion::class,
            User::class,
            UsuarioRol::class,
        ] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
