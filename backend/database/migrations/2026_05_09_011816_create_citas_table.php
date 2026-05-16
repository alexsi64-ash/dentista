<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocio_id')->constrained('negocios');
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes');
            $table->foreignId('servicio_id')->nullable()->constrained('servicios');
            $table->string('nombre_cliente')->nullable(); // Para nuevos
            $table->string('telefono_cliente')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'atendido', 'ocupado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
