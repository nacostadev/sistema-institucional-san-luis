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
            $table->bigIncrements('id_log');
            $table->dateTime('creado_en', 6)->useCurrent();
            $table->unsignedBigInteger('actor_usuario_id')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('tabla', 64);
            $table->unsignedBigInteger('registro_id');
            $table->enum('accion', ['INSERT', 'UPDATE']);
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
