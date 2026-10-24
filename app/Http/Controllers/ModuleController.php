<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

class ModuleController extends Controller
{
    private const MODEL_MAP = [
        'rol' => Rol::class,
        'usuario' => User::class,
        'rol_vista_permiso' => RolVistaPermiso::class,
        'docente' => Docente::class,
        'estudiante' => Estudiante::class,
        'costos' => Costo::class,
        'curso' => Curso::class,
        'aula' => Aula::class,
        'seccion' => Seccion::class,
        'horario_programacion' => HorarioProgramacion::class,
        'matricula' => Matricula::class,
        'evaluacion' => Evaluacion::class,
        'nota' => Nota::class,
        'sesion_clase' => SesionClase::class,
        'asistencia' => Asistencia::class,
        'pago' => Pago::class,
        'vista' => Vista::class,
    ];

    private const MODULES = [
        'roles' => ['table' => 'rol', 'key' => 'id_rol', 'fields' => [
            ['name' => 'nombre_rol', 'label' => 'Nombre del rol'],
            ['name' => 'descripcion', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'usuarios' => ['table' => 'usuario', 'key' => 'id_usuario', 'fields' => [
            ['name' => 'rol_id', 'label' => 'Rol', 'type' => 'relation', 'table' => 'rol', 'value' => 'id_rol', 'display' => 'nombre_rol'],
            ['name' => 'nombre_usuario', 'label' => 'Nombre de usuario'],
            ['name' => 'correo', 'label' => 'Correo'],
            ['name' => 'clave', 'label' => 'Clave', 'type' => 'password'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'permisos' => ['table' => 'rol_vista_permiso', 'key' => 'id_permiso', 'fields' => [
            ['name' => 'rol_id', 'label' => 'Rol', 'type' => 'relation', 'table' => 'rol', 'value' => 'id_rol', 'display' => 'nombre_rol'],
            ['name' => 'vista_id', 'label' => 'Vista', 'type' => 'relation', 'table' => 'vista', 'value' => 'id_vista', 'display' => 'nombre_vista'],
            ['name' => 'puede_crear', 'label' => 'Puede crear', 'type' => 'checkbox'],
            ['name' => 'puede_editar', 'label' => 'Puede editar', 'type' => 'checkbox'],
            ['name' => 'puede_eliminar', 'label' => 'Puede eliminar', 'type' => 'checkbox'],
        ]],
        'docentes' => ['table' => 'docente', 'key' => 'id_docente', 'fields' => [
            ['name' => 'dni', 'label' => 'DNI'],
            ['name' => 'nombre_completo', 'label' => 'Nombre completo'],
            ['name' => 'especialidad', 'label' => 'Especialidad'],
            ['name' => 'telefono', 'label' => 'Telefono'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'estudiantes' => ['table' => 'estudiante', 'key' => 'id_estudiante', 'fields' => [
            ['name' => 'dni', 'label' => 'DNI'],
            ['name' => 'nombre_completo', 'label' => 'Nombre completo'],
            ['name' => 'telefono', 'label' => 'Telefono'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'costos' => ['table' => 'costos', 'key' => 'id_costo', 'fields' => [
            ['name' => 'nombre_costo', 'label' => 'Categoria tarifaria'],
            ['name' => 'monto', 'label' => 'Monto', 'type' => 'number'],
            ['name' => 'descripcion', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'cursos' => ['table' => 'curso', 'key' => 'id_curso', 'fields' => [
            ['name' => 'costo_id', 'label' => 'Tarifa educativa asociada', 'type' => 'relation', 'table' => 'costos', 'value' => 'id_costo', 'display' => 'nombre_costo'],
            ['name' => 'nombre', 'label' => 'Nombre del curso'],
            ['name' => 'descripcion', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'aulas' => ['table' => 'aula', 'key' => 'id_aula', 'fields' => [
            ['name' => 'nombre_aula', 'label' => 'Nombre del aula'],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'options' => ['fisica', 'virtual']],
            ['name' => 'capacidad', 'label' => 'Capacidad', 'type' => 'number'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'secciones' => ['table' => 'seccion', 'key' => 'id_seccion', 'fields' => [
            ['name' => 'curso_id', 'label' => 'Curso', 'type' => 'relation', 'table' => 'curso', 'value' => 'id_curso', 'display' => 'nombre'],
            ['name' => 'docente_id', 'label' => 'Docente responsable', 'type' => 'relation', 'table' => 'docente', 'value' => 'id_docente', 'display' => 'nombre_completo'],
            ['name' => 'nombre_seccion', 'label' => 'Seccion'],
            ['name' => 'periodo_lectivo', 'label' => 'Periodo lectivo'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'horarios' => ['table' => 'horario_programacion', 'key' => 'id_horario', 'fields' => [
            ['name' => 'seccion_id', 'label' => 'Seccion', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre_seccion'],
            ['name' => 'aula_id', 'label' => 'Aula', 'type' => 'relation', 'table' => 'aula', 'value' => 'id_aula', 'display' => 'nombre_aula'],
            ['name' => 'dia_semana', 'label' => 'Dia', 'type' => 'select', 'options' => ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo']],
            ['name' => 'hora_inicio', 'label' => 'Hora de inicio', 'type' => 'time'],
            ['name' => 'hora_fin', 'label' => 'Hora de fin', 'type' => 'time'],
        ]],
        'matriculas' => ['table' => 'matricula', 'key' => 'id_matricula', 'fields' => [
            ['name' => 'estudiante_id', 'label' => 'Estudiante', 'type' => 'relation', 'table' => 'estudiante', 'value' => 'id_estudiante', 'display' => 'nombre_completo'],
            ['name' => 'seccion_id', 'label' => 'Seccion', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre_seccion'],
            ['name' => 'fecha_matricula', 'label' => 'Fecha de matrícula', 'type' => 'date'],
            ['name' => 'estado_matricula', 'label' => 'Estado', 'type' => 'select', 'options' => ['matriculado', 'retirado', 'finalizado', 'inhabilitado'], 'hidden_create' => true],
        ]],
        'evaluaciones' => ['table' => 'evaluacion', 'key' => 'id_evaluacion', 'fields' => [
            ['name' => 'seccion_id', 'label' => 'Seccion', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre_seccion'],
            ['name' => 'nombre_evaluacion', 'label' => 'Nombre'],
            ['name' => 'peso_porcentaje', 'label' => 'Peso porcentual', 'type' => 'number'],
        ]],
        'notas' => ['table' => 'nota', 'key' => 'id_nota', 'fields' => [
            ['name' => 'evaluacion_id', 'label' => 'Evaluacion', 'type' => 'relation', 'table' => 'evaluacion', 'value' => 'id_evaluacion', 'display' => 'nombre_evaluacion'],
            ['name' => 'matricula_id', 'label' => 'Matricula', 'type' => 'relation', 'table' => 'matricula', 'value' => 'id_matricula', 'display' => 'id_matricula'],
            ['name' => 'calificacion', 'label' => 'Calificacion', 'type' => 'number'],
        ]],
        'sesiones' => ['table' => 'sesion_clase', 'key' => 'id_sesion', 'fields' => [
            ['name' => 'seccion_id', 'label' => 'Seccion', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre_seccion'],
            ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date'],
            ['name' => 'tema', 'label' => 'Tema', 'type' => 'textarea'],
        ]],
        'asistencia' => ['table' => 'asistencia', 'key' => 'id_asistencia', 'fields' => [
            ['name' => 'sesion_id', 'label' => 'Sesion', 'type' => 'relation', 'table' => 'sesion_clase', 'value' => 'id_sesion', 'display' => 'id_sesion'],
            ['name' => 'matricula_id', 'label' => 'Matricula', 'type' => 'relation', 'table' => 'matricula', 'value' => 'id_matricula', 'display' => 'id_matricula'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['presente', 'falta', 'tardanza', 'justificado']],
        ]],
        'pagos' => ['table' => 'pago', 'key' => 'id_pago', 'fields' => [
            ['name' => 'matricula_id', 'label' => 'Matrícula del estudiante', 'type' => 'relation', 'table' => 'matricula', 'value' => 'id_matricula', 'display' => 'display_label'],
            ['name' => 'costo_id', 'label' => 'Categoria de costo', 'type' => 'relation', 'table' => 'costos', 'value' => 'id_costo', 'display' => 'nombre_costo'],
            ['name' => 'monto_pagado', 'label' => 'Monto pagado', 'type' => 'number'],
            ['name' => 'fecha_pago', 'label' => 'Fecha de pago', 'type' => 'datetime-local'],
            ['name' => 'metodo_pago', 'label' => 'Metodo', 'type' => 'select', 'options' => ['efectivo', 'tarjeta', 'transferencia', 'yape_plin']],
            ['name' => 'nro_comprobante', 'label' => 'Nro. comprobante'],
        ]],
    ];

    public function index(string $module)
    {
        $this->authorizeModule($module, 'view');
        $user = Auth::user();
        $role = $user?->rol?->nombre_rol ?? 'Invitado';

        $definition = self::MODULES[$module];
        $model = $this->modelForTable($definition['table']);

        $query = $model->newQuery();

        // [VULNERABILIDAD 1: SQLi] Uso de consultas directas sin parametrizar mediante Eloquent
        // ejemplo de ataque: ?buscar= %') OR ('1'='1
        //
        if (request()->filled('buscar')) {
            $buscar = request('buscar');
            $searchField = match ($module) {
                'cursos' => 'nombre',
                'estudiantes' => 'nombre_completo',
                'docentes' => 'nombre_completo',
                'secciones' => 'nombre_seccion',
                'evaluaciones' => 'nombre_evaluacion',
                'sesiones' => 'tema',
                default => null,
            };
            if ($searchField) {
                $query->whereRaw("{$searchField} LIKE '%" . $buscar . "%'");
            }
        }

        // [VULNERABILIDAD 2: IDOR] Omisión en la comprobación de propiedad del recurso en servidor
        // Si se altera el parámetro ?id_alumno en la URL, el servidor devuelve los registros de dicho alumno sin verificar la sesión
        if (request()->filled('id_alumno')) {
            $idAlumno = request('id_alumno');
            $query = match ($module) {
                'matriculas' => $query->where('estudiante_id', $idAlumno),
                'notas' => $query->whereHas('matricula', fn($q) => $q->where('estudiante_id', $idAlumno)),
                'asistencia' => $query->whereHas('matricula', fn($q) => $q->where('estudiante_id', $idAlumno)),
                'cursos' => $query->whereIn('id_curso', Seccion::whereIn('id_seccion', Matricula::where('estudiante_id', $idAlumno)->pluck('seccion_id'))->pluck('curso_id')),
                'estudiantes' => $query->where('id_estudiante', $idAlumno),
                default => $query,
            };
        } else {
            // Filtrado habitual por rol
            $query = $this->scopeQueryByRole($query, $module, $user, $role);
        }

        $rows = $query->orderByDesc($definition['key'])->get();

        // Relaciones
        $relations = $this->relations($definition['fields'], $user, $role);

        // Permisos de UI (Crear, Editar, Eliminar)
        $permissions = $this->getUiPermissions($module, $role);
        $titleInfo = $this->getCustomTitle($module, $role);

        return view('modules.show', [
            'module' => $module,
            'title' => $titleInfo['title'],
            'subtitle' => $titleInfo['subtitle'],
            'definition' => $definition,
            'rows' => $rows,
            'relations' => $relations,
            'canCreate' => $permissions['canCreate'],
            'canEdit' => $permissions['canEdit'],
            'canDelete' => $permissions['canDelete'],
            'userRole' => $role,
        ]);
    }

    public function store(Request $request, string $module)
    {
        $this->authorizeModule($module, 'create');
        $definition = self::MODULES[$module];

        if (in_array($module, ['estudiantes', 'docentes'], true)) {
            return $this->storeProfile($request, $module);
        }

        $model = $this->modelForTable($definition['table']);
        try {
            // [VULNERABILIDAD 4: Mala estructura MVC] Captura directa de datos con $request->all() sin filtros de validación
            $payload = $request->all();
            $payload = $this->applyDefaults($payload, $module);

            if ($module === 'notas' && isset($payload['calificacion'])) {
                $payload['calificacion'] = max(0, min(20, (int) round((float) $payload['calificacion'])));
            }

            DB::transaction(fn () => $model->newQuery()->create($payload));
            return back()->with('message', 'Registro creado correctamente.');
        } catch (\Throwable $exception) {
            report($exception);
            return back()->with('error', 'No se pudo crear el registro. Verifique los datos.');
        }
    }

    public function update(Request $request, string $module, int $id)
    {
        $this->authorizeModule($module, 'edit');
        $definition = self::MODULES[$module];
        $model = $this->modelForTable($definition['table']);

        try {
            $record = $model->newQuery()->where($definition['key'], $id)->firstOrFail();

            // [VULNERABILIDAD 4: Mala estructura MVC] Captura directa con $request->all() sin FormRequest
            $payload = $request->all();

            if ($module === 'notas' && isset($payload['calificacion'])) {
                $payload['calificacion'] = max(0, min(20, (int) round((float) $payload['calificacion'])));
            }

            DB::transaction(fn () => $record->update($payload));

            $user = Auth::user();
            $role = $user?->rol?->nombre_rol ?? '';

            // Si es Alumno, redirigir a la vista de solo lectura (sin el truco de edición),
            // manteniendo el parámetro id_alumno si estaba visualizando a otro estudiante.
            if ($role === 'Alumno') {
                $redirectParams = ['module' => $module];
                if ($request->filled('id_alumno')) {
                    $redirectParams['id_alumno'] = $request->input('id_alumno');
                }
                return redirect()->route('modules.show', $redirectParams)->with('message', 'Calificación actualizada correctamente.');
            }

            return back()->with('message', 'Registro actualizado correctamente.');
        } catch (\Throwable $exception) {
            report($exception);
            return back()->with('error', 'No se pudo actualizar el registro. Verifique los datos.');
        }
    }

    public function destroy(string $module, int $id)
    {
        $this->authorizeModule($module, 'delete');
        $definition = self::MODULES[$module];
        $model = $this->modelForTable($definition['table']);

        // [VULNERABILIDAD 2: IDOR en operaciones] El servidor no valida si el registro específico pertenece al usuario que envía la petición
        $record = $model->newQuery()->where($definition['key'], $id)->firstOrFail();
        $hasState = collect($definition['fields'])->contains('name', 'estado');
        $hasCourseState = collect($definition['fields'])->contains('name', 'estado_matricula');
        DB::transaction(function () use ($record, $hasState, $hasCourseState): void {
            if ($hasState) {
                $record->update(['estado' => 'inactivo']);
            } elseif ($hasCourseState) {
                $record->update(['estado_matricula' => 'inhabilitado']);
            } else {
                $record->delete();
            }
        });
        return back()->with('message', 'Registro actualizado.');
    }

    private function scopeQueryByRole($query, string $module, User $user, string $role)
    {
        if ($role === 'Docente') {
            $docente = $user->docente;
            if (!$docente) {
                return $query->whereNull('id_' . Str::singular($module));
            }
            $seccionIds = Seccion::where('docente_id', $docente->id_docente)->pluck('id_seccion');
            $cursoIds = Seccion::where('docente_id', $docente->id_docente)->pluck('curso_id')->unique();

            return match ($module) {
                'cursos' => $query->whereIn('id_curso', $cursoIds),
                'secciones' => $query->where('docente_id', $docente->id_docente),
                'horarios' => $query->whereIn('seccion_id', $seccionIds),
                'estudiantes' => $query->whereHas('matriculas', fn($q) => $q->whereIn('seccion_id', $seccionIds)),
                'evaluaciones' => $query->whereIn('seccion_id', $seccionIds),
                'sesiones' => $query->whereIn('seccion_id', $seccionIds),
                'matriculas' => $query->whereIn('seccion_id', $seccionIds),
                'notas' => $query->whereHas('evaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds)),
                'asistencia' => $query->whereHas('sesion', fn($q) => $q->whereIn('seccion_id', $seccionIds)),
                default => $query,
            };
        }

        if ($role === 'Alumno') {
            $estudiante = $user->estudiante;
            if (!$estudiante) {
                return $query->whereNull('id_' . Str::singular($module));
            }
            $matriculas = Matricula::where('estudiante_id', $estudiante->id_estudiante)->get();
            $matriculaIds = $matriculas->pluck('id_matricula');
            $seccionIds = $matriculas->pluck('seccion_id');
            $cursoIds = Seccion::whereIn('id_seccion', $seccionIds)->pluck('curso_id')->unique();

            return match ($module) {
                'cursos' => $query->whereIn('id_curso', $cursoIds),
                'matriculas' => $query->where('estudiante_id', $estudiante->id_estudiante),
                'secciones' => $query->whereIn('id_seccion', $seccionIds),
                'horarios' => $query->whereIn('seccion_id', $seccionIds),
                'evaluaciones' => $query->whereIn('seccion_id', $seccionIds),
                'sesiones' => $query->whereIn('seccion_id', $seccionIds),
                'notas' => $query->whereIn('matricula_id', $matriculaIds),
                'asistencia' => $query->whereIn('matricula_id', $matriculaIds),
                default => $query,
            };
        }

        return $query;
    }

    private function getUiPermissions(string $module, string $role): array
    {
        if ($role === 'Administrador') {
            return ['canCreate' => true, 'canEdit' => true, 'canDelete' => true];
        }

        if ($role === 'Coordinador') {
            $readOnly = ['roles', 'usuarios', 'permisos', 'costos', 'pagos'];
            $isReadOnly = in_array($module, $readOnly, true);
            return [
                'canCreate' => !$isReadOnly,
                'canEdit' => !$isReadOnly,
                'canDelete' => !$isReadOnly,
            ];
        }

        if ($role === 'Docente') {
            $canManage = in_array($module, ['evaluaciones', 'notas', 'sesiones', 'asistencia'], true);
            return [
                'canCreate' => $canManage,
                'canEdit' => $canManage,
                'canDelete' => false,
            ];
        }

        if ($role === 'Alumno') {
            // [VULNERABILIDAD LÓGICA / CONTROL DE ACCESO]: Permite al alumno editar el registro de notas
            // SOLO cuando se active mediante el "truco" (ej. ?editar=1, ?edit=1, ?truco=1, ?modo=editar).
            // Por defecto, o tras actualizar la página / guardar cambios, permanece en modo sólo lectura.
            $trucoActivo = request()->has('editar') || request()->has('edit') || request()->has('truco') || request('modo') === 'editar';
            return [
                'canCreate' => false,
                'canEdit' => ($module === 'notas' && $trucoActivo),
                'canDelete' => false,
            ];
        }

        return ['canCreate' => false, 'canEdit' => false, 'canDelete' => false];
    }

    private function getCustomTitle(string $module, string $role): array
    {
        $baseTitle = Str::of($module)->replace('-', ' ')->title()->toString();

        if ($role === 'Docente') {
            return match ($module) {
                'cursos' => ['title' => 'Mis Cursos Asignados', 'subtitle' => 'Cursos a tu cargo en el presente periodo lectivo'],
                'secciones' => ['title' => 'Mis Secciones', 'subtitle' => 'Grupos académicos asignados'],
                'horarios' => ['title' => 'Mis Horarios de Clase', 'subtitle' => 'Programación semanal de tus clases'],
                'estudiantes' => ['title' => 'Mis Estudiantes', 'subtitle' => 'Alumnos matriculados en tus secciones'],
                'evaluaciones' => ['title' => 'Evaluaciones de Mis Cursos', 'subtitle' => 'Gestión de evaluaciones y pesos porcentuales'],
                'notas' => ['title' => 'Registro de Calificaciones', 'subtitle' => 'Notas registradas para tus alumnos'],
                'sesiones' => ['title' => 'Sesiones de Clase', 'subtitle' => 'Bitácora y temas de clase desarrollados'],
                'asistencia' => ['title' => 'Control de Asistencia', 'subtitle' => 'Registro de asistencia de tus alumnos'],
                default => ['title' => $baseTitle, 'subtitle' => "Registros de {$baseTitle}"],
            };
        }

        if ($role === 'Alumno') {
            return match ($module) {
                'cursos' => ['title' => 'Mis Cursos Habilitados', 'subtitle' => 'Cursos en los que te encuentras matriculado actualmente'],
                'matriculas' => ['title' => 'Mis Matrículas', 'subtitle' => 'Historial de matrículas y estado académico'],
                'horarios' => ['title' => 'Mi Horario Semanal', 'subtitle' => 'Horarios y aulas de tus cursos matriculados'],
                'evaluaciones' => ['title' => 'Mis Evaluaciones Programadas', 'subtitle' => 'Evaluaciones de tus cursos matriculados'],
                'notas' => ['title' => 'Mis Calificaciones', 'subtitle' => 'Notas obtenidas en tus cursos'],
                'sesiones' => ['title' => 'Sesiones de Mis Cursos', 'subtitle' => 'Fechas y temas tratados en tus clases'],
                'asistencia' => ['title' => 'Mi Registro de Asistencia', 'subtitle' => 'Asistencias y tardanzas registradas'],
                default => ['title' => $baseTitle, 'subtitle' => "Registros de {$baseTitle}"],
            };
        }

        return [
            'title' => $baseTitle,
            'subtitle' => "Gestión de {$baseTitle} en el sistema",
        ];
    }

    private function relations(array $fields, ?User $user = null, ?string $role = null): array
    {
        $relations = [];
        $docenteSeccionIds = [];
        if ($role === 'Docente' && $user && $user->docente) {
            $docenteSeccionIds = Seccion::where('docente_id', $user->docente->id_docente)->pluck('id_seccion')->toArray();
        }

        foreach ($fields as $field) {
            if (($field['type'] ?? '') === 'relation') {
                $model = $this->modelForTable($field['table']);
                $query = $model->newQuery();

                if ($role === 'Docente') {
                    if ($field['name'] === 'seccion_id') {
                        $query->whereIn('id_seccion', $docenteSeccionIds);
                    } elseif ($field['name'] === 'evaluacion_id') {
                        $query->whereIn('seccion_id', $docenteSeccionIds);
                    } elseif ($field['name'] === 'sesion_id') {
                        $query->whereIn('seccion_id', $docenteSeccionIds);
                    } elseif ($field['name'] === 'matricula_id') {
                        $query->whereIn('seccion_id', $docenteSeccionIds);
                    }
                }

                $relations[$field['name']] = $query->orderBy($field['value'])->get();
                if ($field['name'] === 'matricula_id') {
                    $relations[$field['name']]->each(function ($item) {
                        $item->display_label = sprintf(
                            'Matrícula #%s - %s (%s)',
                            $item->id_matricula,
                            $item->estudiante?->nombre_completo ?? 'Estudiante',
                            $item->seccion?->nombre_seccion ?? 'Sección'
                        );
                    });
                }
                if ($field['table'] === 'costos') {
                    $relations[$field['name']]->each(function ($item) {
                        $item->display_label = sprintf('Tarifa: %s | S/ %s', $item->nombre_costo, number_format((float) $item->monto, 2));
                    });
                }
                if ($field['table'] === 'seccion') {
                    $relations[$field['name']]->each(function ($item) {
                        $item->display_label = sprintf('%s | %s (Periodo %s)', $item->nombre_seccion, $item->curso?->nombre ?? 'Curso', $item->periodo_lectivo);
                    });
                }
            }
        }
        return $relations;
    }

    private function modelForTable(string $table)
    {
        return new (self::MODEL_MAP[$table]);
    }

    private function applyDefaults(array $payload, string $module): array
    {
        if (in_array($module, ['roles', 'usuarios', 'docentes', 'estudiantes', 'costos', 'cursos', 'aulas', 'secciones'], true)) {
            $payload['estado'] = $payload['estado'] ?? 'activo';
        }
        if ($module === 'matriculas') {
            $payload['fecha_matricula'] = $payload['fecha_matricula'] ?? now()->toDateString();
            $payload['estado_matricula'] = $payload['estado_matricula'] ?? 'matriculado';
        }
        if ($module === 'asistencia') {
            $payload['estado'] = $payload['estado'] ?? 'presente';
        }
        return $payload;
    }

    private function storeProfile(Request $request, string $module)
    {
        return DB::transaction(function () use ($request, $module) {
            // [VULNERABILIDAD 4: Sin FormRequest] Captura directa de datos
            $data = $request->all();
            $roleName = $module === 'estudiantes' ? 'Alumno' : 'Docente';
            $role = Rol::where('nombre_rol', $roleName)->firstOrFail();
            $parts = preg_split('/\s+/', trim($data['nombre_completo'] ?? 'usuario nuevo'));
            $username = strtolower(($parts[0] ?? 'usuario') . '.' . ($parts[1] ?? 'nuevo'));
            $username = preg_replace('/[^a-z0-9.]/', '', Str::ascii($username));
            $baseUsername = $username;
            $counter = 1;
            while (User::where('nombre_usuario', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }
            // [VULNERABILIDAD 5 & 6: Clave en texto plano "123" y sin complejidad]
            $user = User::create([
                'rol_id' => $role->id_rol,
                'nombre_usuario' => $username,
                'correo' => $username . '@instituto.edu.pe',
                'clave' => '123',
                'estado' => 'activo',
            ]);
            $data['usuario_id'] = $user->id_usuario;
            $data['estado'] = 'activo';
            $profile = $module === 'estudiantes' ? Estudiante::class : Docente::class;
            $profile::create($data);

            return back()->with('message', "Registro creado. Usuario: {$username} | Clave inicial: 123");
        });
    }

    private function authorizeModule(string $module, string $action = 'view'): void
    {
        $user = Auth::user();
        $role = $user?->rol?->nombre_rol;

        $docenteAllowed = ['cursos', 'secciones', 'horarios', 'estudiantes', 'evaluaciones', 'notas', 'sesiones', 'asistencia'];
        $alumnoAllowed = ['cursos', 'matriculas', 'horarios', 'evaluaciones', 'notas', 'sesiones', 'asistencia'];
        $coordinatorAllowed = ['estudiantes', 'docentes', 'cursos', 'secciones', 'aulas', 'horarios', 'matriculas', 'evaluaciones', 'notas', 'sesiones', 'asistencia'];

        $canView = match ($role) {
            'Administrador' => true,
            'Coordinador' => in_array($module, $coordinatorAllowed, true),
            'Docente' => in_array($module, $docenteAllowed, true),
            'Alumno' => in_array($module, $alumnoAllowed, true),
            default => false,
        };

        abort_unless($canView, 403, 'No tiene acceso a este módulo.');
    }
}
