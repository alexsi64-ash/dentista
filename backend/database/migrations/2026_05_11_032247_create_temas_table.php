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
        Schema::create('temas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color_primario', 7);
            $table->string('color_secundario', 7);
            $table->string('color_fondo', 7)->default('#F8FAFC');
            $table->timestamps();
        });

        Schema::create('negocio_tema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocio_id')->constrained('negocios')->onDelete('cascade');
            $table->foreignId('tema_id')->constrained('temas')->onDelete('cascade');
        
            // Aquí desacoplamos la tipografía del tema
            $table->string('tipografia')->default('Inter'); 
            $table->boolean('activo')->default(true); // Para saber qué configuración usa actualmente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temas');
        Schema::dropIfExists('negocio_tema');
    }
};
