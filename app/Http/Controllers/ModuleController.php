<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

class ModuleController extends Controller
{
    private const MODEL_MAP = [
        'rol' => Rol::class,
        'usuario' => User::class,
        'docente' => Docente::class,
        'estudiante' => Estudiante::class,
        'costo' => Costo::class,
        'curso' => Curso::class,
        'aula' => Aula::class,
        'seccion' => Seccion::class,
        'horario' => Horario::class,
        'matricula' => Matricula::class,
        'evaluacion' => Evaluacion::class,
        'nota' => Nota::class,
        'pago' => Pago::class,
        'cargo_adicional' => CargoAdicional::class,
        'periodo' => Periodo::class,
        'programa' => Programa::class,
        'docente_habilitacion' => DocenteHabilitacion::class,
        'usuario_rol' => UsuarioRol::class,
    ];

    private const MODULES = [
        'roles' => ['table' => 'rol', 'key' => 'id_rol', 'fields' => [
            ['name' => 'codigo', 'label' => 'Código del rol'],
            ['name' => 'descripcion', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'usuarios' => ['table' => 'usuario', 'key' => 'id_usuario', 'fields' => [
            ['name' => 'nombre_usuario', 'label' => 'Nombre de usuario'],
            ['name' => 'dni', 'label' => 'DNI'],
            ['name' => 'correo', 'label' => 'Correo'],
            ['name' => 'nombres', 'label' => 'Nombres'],
            ['name' => 'apellidos', 'label' => 'Apellidos'],
            ['name' => 'telefono', 'label' => 'Teléfono'],
            ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'docentes' => ['table' => 'docente', 'key' => 'id_docente', 'fields' => [
            ['name' => 'usuario_id', 'label' => 'Usuario', 'type' => 'relation', 'table' => 'usuario', 'value' => 'id_usuario', 'display' => 'nombre_usuario'],
            ['name' => 'especialidad', 'label' => 'Especialidad'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'estudiantes' => ['table' => 'estudiante', 'key' => 'id_estudiante', 'fields' => [
            ['name' => 'usuario_id', 'label' => 'Usuario', 'type' => 'relation', 'table' => 'usuario', 'value' => 'id_usuario', 'display' => 'nombre_usuario'],
            ['name' => 'codigo_estudiante', 'label' => 'Código de estudiante'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'costos' => ['table' => 'costo', 'key' => 'id_costo', 'fields' => [
            ['name' => 'nombre_concepto', 'label' => 'Concepto'],
            ['name' => 'categoria', 'label' => 'Categoría', 'type' => 'select', 'options' => ['curso', 'pension', 'tramite', 'otro']],
            ['name' => 'monto', 'label' => 'Monto', 'type' => 'number'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'cursos' => ['table' => 'curso', 'key' => 'id_curso', 'fields' => [
            ['name' => 'programa_id', 'label' => 'Programa', 'type' => 'relation', 'table' => 'programa', 'value' => 'id_programa', 'display' => 'nombre'],
            ['name' => 'costo_id', 'label' => 'Costo asociado', 'type' => 'relation', 'table' => 'costo', 'value' => 'id_costo', 'display' => 'nombre_concepto'],
            ['name' => 'nombre', 'label' => 'Nombre del curso'],
            ['name' => 'descripcion', 'label' => 'Descripcion', 'type' => 'textarea'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'aulas' => ['table' => 'aula', 'key' => 'id_aula', 'fields' => [
            ['name' => 'nombre', 'label' => 'Nombre del aula'],
            ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'options' => ['fisica', 'virtual']],
            ['name' => 'capacidad', 'label' => 'Capacidad', 'type' => 'number'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'secciones' => ['table' => 'seccion', 'key' => 'id_seccion', 'fields' => [
            ['name' => 'curso_id', 'label' => 'Curso', 'type' => 'relation', 'table' => 'curso', 'value' => 'id_curso', 'display' => 'nombre'],
            ['name' => 'periodo_id', 'label' => 'Periodo', 'type' => 'relation', 'table' => 'periodo', 'value' => 'id_periodo', 'display' => 'codigo'],
            ['name' => 'docente_id', 'label' => 'Docente responsable', 'type' => 'relation', 'table' => 'docente', 'value' => 'id_docente', 'display' => 'nombre_completo'],
            ['name' => 'nombre', 'label' => 'Sección'],
            ['name' => 'cupo_maximo', 'label' => 'Cupo máximo', 'type' => 'number'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['planificada', 'abierta', 'cerrada', 'cancelada']],
        ]],
        'horarios' => ['table' => 'horario', 'key' => 'id_horario', 'fields' => [
            ['name' => 'seccion_id', 'label' => 'Sección', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre'],
            ['name' => 'aula_id', 'label' => 'Aula', 'type' => 'relation', 'table' => 'aula', 'value' => 'id_aula', 'display' => 'nombre'],
            ['name' => 'dia_semana', 'label' => 'Día de semana', 'type' => 'number'],
            ['name' => 'hora_inicio', 'label' => 'Hora de inicio', 'type' => 'time'],
            ['name' => 'hora_fin', 'label' => 'Hora de fin', 'type' => 'time'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activo', 'inactivo']],
        ]],
        'matriculas' => ['table' => 'matricula', 'key' => 'id_matricula', 'fields' => [
            ['name' => 'estudiante_id', 'label' => 'Estudiante', 'type' => 'relation', 'table' => 'estudiante', 'value' => 'id_estudiante', 'display' => 'nombre_completo'],
            ['name' => 'seccion_id', 'label' => 'Sección', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre'],
            ['name' => 'fecha_matricula', 'label' => 'Fecha de matrícula', 'type' => 'date'],
            ['name' => 'precio_aplicado', 'label' => 'Precio aplicado', 'type' => 'number'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['formalizada', 'retirada', 'anulada']],
        ]],
        'evaluaciones' => ['table' => 'evaluacion', 'key' => 'id_evaluacion', 'fields' => [
            ['name' => 'seccion_id', 'label' => 'Sección', 'type' => 'relation', 'table' => 'seccion', 'value' => 'id_seccion', 'display' => 'nombre'],
            ['name' => 'titulo', 'label' => 'Título'],
            ['name' => 'ponderacion', 'label' => 'Ponderación', 'type' => 'number'],
            ['name' => 'fecha_evaluacion', 'label' => 'Fecha', 'type' => 'date'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['activa', 'anulada']],
        ]],
        'notas' => ['table' => 'nota', 'key' => 'id_nota', 'fields' => [
            ['name' => 'evaluacion_id', 'label' => 'Evaluación', 'type' => 'relation', 'table' => 'evaluacion', 'value' => 'id_evaluacion', 'display' => 'titulo'],
            ['name' => 'matricula_id', 'label' => 'Matricula', 'type' => 'relation', 'table' => 'matricula', 'value' => 'id_matricula', 'display' => 'id_matricula'],
            ['name' => 'calificacion', 'label' => 'Calificacion', 'type' => 'number'],
        ]],
        'pagos' => ['table' => 'pago', 'key' => 'id_pago', 'fields' => [
            ['name' => 'matricula_id', 'label' => 'Matrícula del estudiante', 'type' => 'relation', 'table' => 'matricula', 'value' => 'id_matricula', 'display' => 'display_label'],
            ['name' => 'cargo_id', 'label' => 'Cargo', 'type' => 'relation', 'table' => 'cargo_adicional', 'value' => 'id_cargo', 'display' => 'referencia_interna'],
            ['name' => 'monto', 'label' => 'Monto', 'type' => 'number'],
            ['name' => 'fecha_pago', 'label' => 'Fecha de pago', 'type' => 'datetime-local'],
            ['name' => 'serie_comprobante', 'label' => 'Serie'],
            ['name' => 'numero_comprobante', 'label' => 'Número de comprobante'],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => ['pendiente', 'validado', 'anulado']],
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
                'secciones' => 'nombre',
                'evaluaciones' => 'titulo',
                default => null,
            };
            if ($searchField) {
                $query->where($searchField, 'like', '%' . $buscar . '%');
            }
        }

        // [VULNERABILIDAD 2: IDOR] Omisión en la comprobación de propiedad del recurso en servidor
        // Si se altera el parámetro ?id_alumno en la URL, el servidor devuelve los registros de dicho alumno sin verificar la sesión
        if (request()->filled('id_alumno')) {
            $idAlumno = request('id_alumno');
            $query = match ($module) {
                'matriculas' => $query->where('estudiante_id', $idAlumno),
                'notas' => $query->whereHas('matricula', fn($q) => $q->where('estudiante_id', $idAlumno)),
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

        if ($module === 'usuarios') {
            $request->validate(['password' => ['required', 'string', 'min:12']]);
        }

        $model = $this->modelForTable($definition['table']);
        try {
            // [VULNERABILIDAD 4: Mala estructura MVC] Captura directa de datos con $request->all() sin filtros de validación
            $payload = $request->all();
            $payload = $this->applyDefaults($payload, $module);

            if ($module === 'notas' && isset($payload['calificacion'])) {
                $payload['calificacion'] = max(0, min(20, (float) $payload['calificacion']));
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

        if ($module === 'usuarios' && $request->filled('password')) {
            $request->validate(['password' => ['string', 'min:12']]);
        }

        try {
            $record = $model->newQuery()->where($definition['key'], $id)->firstOrFail();

            // [VULNERABILIDAD 4: Mala estructura MVC] Captura directa con $request->all() sin FormRequest
            $payload = $request->all();
            if ($module === 'usuarios') {
                if (! $request->filled('password')) {
                    unset($payload['password']);
                }
            }

            if ($module === 'notas' && isset($payload['calificacion'])) {
                $payload['calificacion'] = max(0, min(20, (float) $payload['calificacion']));
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
        DB::transaction(function () use ($record, $hasState, $module): void {
            if ($hasState) {
                $record->update(['estado' => $module === 'matriculas' ? 'anulada' : 'inactivo']);
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
                'matriculas' => $query->whereIn('seccion_id', $seccionIds),
                'notas' => $query->whereHas('evaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds)),
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
                'notas' => $query->whereIn('matricula_id', $matriculaIds),
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
            $readOnly = ['roles', 'usuarios', 'costos', 'pagos'];
            $isReadOnly = in_array($module, $readOnly, true);
            return [
                'canCreate' => !$isReadOnly,
                'canEdit' => !$isReadOnly,
                'canDelete' => !$isReadOnly,
            ];
        }

        if ($role === 'Docente') {
            $canManage = in_array($module, ['evaluaciones', 'notas'], true);
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
                            $item->seccion?->nombre ?? 'Sección'
                        );
                    });
                }
                if ($field['table'] === 'costo') {
                    $relations[$field['name']]->each(function ($item) {
                        $item->display_label = sprintf('%s | S/ %s', $item->nombre_concepto, number_format((float) $item->monto, 2));
                    });
                }
                if ($field['table'] === 'seccion') {
                    $relations[$field['name']]->each(function ($item) {
                        $item->display_label = sprintf('%s | %s (Periodo %s)', $item->nombre, $item->curso?->nombre ?? 'Curso', $item->periodo?->codigo ?? '—');
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
        $payload['estado'] ??= match ($module) {
            'roles', 'usuarios', 'docentes', 'estudiantes', 'costos', 'cursos', 'aulas', 'horarios' => 'activo',
            'secciones' => 'planificada',
            'evaluaciones' => 'activa',
            'matriculas' => 'formalizada',
            'pagos' => 'pendiente',
            default => null,
        };
        if ($module === 'matriculas') {
            $payload['fecha_matricula'] = $payload['fecha_matricula'] ?? now()->toDateString();
            $payload['registrado_por'] = Auth::id();
        }
        if ($module === 'notas') {
            $payload['registrada_por'] = Auth::id();
            if (isset($payload['evaluacion_id'])) {
                $payload['seccion_id'] = Evaluacion::whereKey($payload['evaluacion_id'])->value('seccion_id');
            }
        }
        if ($module === 'pagos') {
            $payload['registrado_por'] = Auth::id();
        }
        return $payload;
    }

    private function authorizeModule(string $module, string $action = 'view'): void
    {
        $user = Auth::user();
        $role = $user?->rol?->nombre_rol;

        $docenteAllowed = ['cursos', 'secciones', 'horarios', 'estudiantes', 'evaluaciones', 'notas'];
        $alumnoAllowed = ['cursos', 'matriculas', 'horarios', 'evaluaciones', 'notas'];
        $coordinatorAllowed = ['estudiantes', 'docentes', 'cursos', 'secciones', 'aulas', 'horarios', 'matriculas', 'evaluaciones', 'notas'];

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
