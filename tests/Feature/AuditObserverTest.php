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

        config(['seguridad.pii_hmac_key' => 'test-hmac-key']);

        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('usuario');

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->dateTime('creado_en', 6);
            $table->unsignedBigInteger('actor_usuario_id')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('tabla', 64);
            $table->unsignedBigInteger('registro_id');
            $table->enum('accion', ['INSERT', 'UPDATE']);
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
        });

        Schema::create('rol', function (Blueprint $table) {
            $table->bigIncrements('id_rol');
            $table->string('codigo', 30);
            $table->string('descripcion', 160);
            $table->enum('estado', ['activo', 'inactivo']);
        });

        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('nombre_usuario', 80);
            $table->text('dni_cifrado')->nullable();
            $table->char('dni_hash', 64)->nullable();
            $table->text('correo_cifrado')->nullable();
            $table->char('correo_hash', 64)->nullable();
            $table->text('nombres_cifrados')->nullable();
            $table->text('apellidos_cifrados')->nullable();
            $table->text('telefono_cifrado')->nullable();
            $table->string('password');
            $table->enum('estado', ['activo', 'inactivo']);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function test_it_logs_insert_and_update_using_the_current_audit_columns(): void
    {
        $actor = User::create([
            'nombre_usuario' => 'admin',
            'correo' => 'ADMIN@example.test',
            'password' => 'hashed-password',
            'estado' => 'activo',
        ]);
        $this->actingAs($actor);

        $role = Rol::create([
            'codigo' => 'COORDINADOR',
            'descripcion' => 'Rol inicial',
            'estado' => 'activo',
        ]);

        $insertLog = DB::table('audit_logs')
            ->where('tabla', 'rol')
            ->where('accion', 'INSERT')
            ->first();

        $this->assertSame($actor->id_usuario, $insertLog->actor_usuario_id);

        $role->update(['descripcion' => 'Rol actualizado']);

        $updateLog = DB::table('audit_logs')
            ->where('tabla', 'rol')
            ->where('accion', 'UPDATE')
            ->first();

        $this->assertSame('Rol inicial', json_decode($updateLog->valores_anteriores, true)['descripcion']);
        $this->assertSame('Rol actualizado', json_decode($updateLog->valores_nuevos, true)['descripcion']);

        $role->delete();

        $this->assertSame(0, DB::table('audit_logs')->where('tabla', 'rol')->where('accion', 'DELETE')->count());
    }

    public function test_it_never_writes_password_or_encrypted_pii_to_audit_snapshots(): void
    {
        User::create([
            'nombre_usuario' => 'admin',
            'dni' => '12345678',
            'correo' => 'ADMIN@example.test',
            'nombres' => 'Nombre de prueba',
            'apellidos' => 'Apellido de prueba',
            'telefono' => '999888777',
            'password' => 'secret-password',
            'estado' => 'activo',
        ]);

        $snapshot = DB::table('audit_logs')
            ->where('tabla', 'usuario')
            ->value('valores_nuevos');

        $this->assertStringNotContainsString('secret-password', $snapshot);
        $this->assertStringNotContainsString('12345678', $snapshot);
        $this->assertStringNotContainsString('ADMIN@example.test', $snapshot);
        $this->assertStringNotContainsString('correo_cifrado', $snapshot);
        $this->assertStringNotContainsString('dni_hash', $snapshot);
    }
}
