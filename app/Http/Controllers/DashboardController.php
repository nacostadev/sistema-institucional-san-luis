<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Matricula;
use App\Models\Nota;
use App\Models\Seccion;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user?->rol?->nombre_rol ?? 'Invitado';

        if ($role === 'Docente') {
            $docente = $user->docente;
            $seccionIds = $docente ? Seccion::where('docente_id', $docente->id_docente)->pluck('id_seccion') : collect();
            $cursoIds = $docente ? Seccion::where('docente_id', $docente->id_docente)->pluck('curso_id')->unique() : collect();
            $estudiantesCount = Estudiante::whereHas('matriculas', fn($q) => $q->whereIn('seccion_id', $seccionIds))->count();
            $evaluacionesCount = Evaluacion::whereIn('seccion_id', $seccionIds)->count();

            $counts = [
                'cursos' => $cursoIds->count(),
                'secciones' => $seccionIds->count(),
                'estudiantes' => $estudiantesCount,
                'evaluaciones' => $evaluacionesCount,
            ];

            $cards = [
                ['cursos', 'Mis Cursos asignados', 'bx-book-open', 'primary', route('modules.show', 'cursos')],
                ['secciones', 'Mis Secciones', 'bx-category', 'info', route('modules.show', 'secciones')],
                ['estudiantes', 'Mis Estudiantes', 'bx-group', 'success', route('modules.show', 'estudiantes')],
                ['evaluaciones', 'Mis Evaluaciones', 'bx-edit', 'warning', route('modules.show', 'evaluaciones')],
            ];

            $welcomeMsg = "Bienvenido, {$docente?->nombre_completo}";
            $welcomeDesc = "Accede a tus cursos asignados, gestiona evaluaciones y registra calificaciones de tus alumnos.";
        } elseif ($role === 'Alumno') {
            $estudiante = $user->estudiante;
            $matriculas = $estudiante ? Matricula::where('estudiante_id', $estudiante->id_estudiante)->get() : collect();
            $seccionIds = $matriculas->pluck('seccion_id');
            $cursoIds = Seccion::whereIn('id_seccion', $seccionIds)->pluck('curso_id')->unique();
            $evaluacionesCount = Evaluacion::whereIn('seccion_id', $seccionIds)->count();
            $notasCount = Nota::whereIn('matricula_id', $matriculas->pluck('id_matricula'))->count();

            $counts = [
                'cursos' => $cursoIds->count(),
                'matriculas' => $matriculas->count(),
                'evaluaciones' => $evaluacionesCount,
                'notas' => $notasCount,
            ];

            $cards = [
                ['cursos', 'Mis Cursos habilitados', 'bx-book-open', 'primary', route('modules.show', 'cursos')],
                ['matriculas', 'Mis Matrículas', 'bx-clipboard', 'info', route('modules.show', 'matriculas')],
                ['evaluaciones', 'Mis Evaluaciones', 'bx-edit', 'warning', route('modules.show', 'evaluaciones')],
                ['notas', 'Mis Calificaciones', 'bx-bar-chart-alt-2', 'success', route('modules.show', 'notas')],
            ];

            $welcomeMsg = "Bienvenido, {$estudiante?->nombre_completo}";
            $welcomeDesc = "Consulta tus cursos matriculados, calificaciones y horarios de clase en tiempo real.";
        } else {
            // Administrador y Coordinador
            $counts = [
                'estudiantes' => Estudiante::count(),
                'docentes' => Docente::count(),
                'cursos' => Curso::count(),
                'matriculas' => Matricula::count(),
            ];

            $cards = [
                ['estudiantes', 'Estudiantes registrados', 'bx-group', 'primary', route('modules.show', 'estudiantes')],
                ['docentes', 'Docentes activos', 'bx-user-voice', 'success', route('modules.show', 'docentes')],
                ['cursos', 'Cursos institucionales', 'bx-book-open', 'info', route('modules.show', 'cursos')],
                ['matriculas', 'Matrículas vigentes', 'bx-clipboard', 'warning', route('modules.show', 'matriculas')],
            ];

            $welcomeMsg = "Bienvenido a Instituto San Luis";
            $welcomeDesc = "Plataforma institucional de gestión académica, seguimiento estudiantil y control administrativo.";
        }

        return view('dashboard', compact('counts', 'cards', 'role', 'welcomeMsg', 'welcomeDesc'));
    }

    public function module(string $module)
    {
        return view('modules.show', [
            'module' => $module,
            'title' => str($module)->replace('-', ' ')->title(),
        ]);
    }
}
