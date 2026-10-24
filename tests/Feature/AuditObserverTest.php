<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuditObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('usuario');

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id_audit');
            $table->unsignedInteger('usuario_id')->nullable();
            $table->string('tabla_afectada', 100);
            $table->bigInteger('registro_id');
            $table->string('accion', 10);
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamp('fecha_evento')->useCurrent();
            $table->string('ip_origen', 45)->nullable();
        });

        Schema::create('rol', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre_rol');
            $table->text('descripcion')->nullable();
            $table->string('estado')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
        });

        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('rol_id')->nullable();
            $table->string('nombre_usuario');
            $table->string('correo');
            $table->string('clave');
            $table->string('estado');
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
        });
    }

    public function test_it_logs_crud_actions_and_tracks_the_authenticated_user(): void
    {
        $actor = User::create([
            'rol_id' => 1,
            'nombre_usuario' => 'admin',
            'correo' => 'admin@example.test',
            'clave' => 'test-secret',
            'estado' => 'activo',
        ]);
        $this->actingAs($actor);

        $role = Rol::create([
            'nombre_rol' => 'Coordinador',
            'descripcion' => 'Rol inicial',
            'estado' => 'activo',
        ]);

        $insertLog = DB::table('audit_logs')
            ->where('tabla_afectada', 'rol')
            ->where('accion', 'INSERT')
            ->first();

        $this->assertSame($actor->id_usuario, $insertLog->usuario_id);
        $this->assertSame($actor->id_usuario, $role->created_by);
        $this->assertSame($actor->id_usuario, $role->updated_by);

        $role->update(['descripcion' => 'Rol actualizado']);

        $updateLog = DB::table('audit_logs')
            ->where('tabla_afectada', 'rol')
            ->where('accion', 'UPDATE')
            ->first();

        $this->assertSame('Rol inicial', json_decode($updateLog->datos_anteriores, true)['descripcion']);
        $this->assertSame('Rol actualizado', json_decode($updateLog->datos_nuevos, true)['descripcion']);
        $this->assertSame($actor->id_usuario, $role->updated_by);

        $role->delete();

        $this->assertDatabaseHas('audit_logs', [
            'tabla_afectada' => 'rol',
            'registro_id' => $role->id_rol,
            'accion' => 'DELETE',
        ]);
    }

    public function test_it_never_writes_passwords_to_audit_snapshots(): void
    {
        User::create([
            'rol_id' => 1,
            'nombre_usuario' => 'admin',
            'correo' => 'admin@example.test',
            'clave' => 'test-secret',
            'estado' => 'activo',
        ]);

        $snapshot = DB::table('audit_logs')
            ->where('tabla_afectada', 'usuario')
            ->value('datos_nuevos');

        $this->assertStringNotContainsString('clave', $snapshot);
        $this->assertStringNotContainsString('test-secret', $snapshot);
    }
}
