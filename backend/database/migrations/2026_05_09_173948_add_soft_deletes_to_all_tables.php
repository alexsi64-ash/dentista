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
    // Lista de tablas que requieren borrado lógico
        $tablas = [
            'negocios', 
            'users', 
            'pacientes', 
            'servicios', 
            'citas', 
            'consultas', 
            'ventas'
        ];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->softDeletes(); // Añade la columna deleted_at
            });
        }
    }

    public function down(): void
    {
        $tablas = [
            'negocios', 
            'users', 
            'pacientes', 
            'servicios', 
            'citas', 
            'consultas', 
            'ventas'
        ];

        foreach ($tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
