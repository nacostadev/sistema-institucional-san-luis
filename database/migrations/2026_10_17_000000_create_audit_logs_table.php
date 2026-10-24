<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id_audit');
            $table->integer('usuario_id')->nullable();
            $table->string('tabla_afectada', 100);
            $table->bigInteger('registro_id');
            $table->enum('accion', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamp('fecha_evento')->useCurrent();
            $table->string('ip_origen', 45)->nullable();

            if (Schema::hasTable('usuario')) {
                $table->foreign('usuario_id', 'fk_audit_usuario')
                    ->references('id_usuario')
                    ->on('usuario')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
