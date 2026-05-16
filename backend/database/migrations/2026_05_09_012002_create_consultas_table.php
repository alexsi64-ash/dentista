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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocio_id')->constrained('negocios');
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('especialista_id')->constrained('users');
            $table->foreignId('servicio_id')->nullable()->constrained('servicios');
            $table->text('observaciones')->nullable();
            $table->text('procedimiento_realizado')->nullable();
            $table->dateTime('fecha_atencion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
