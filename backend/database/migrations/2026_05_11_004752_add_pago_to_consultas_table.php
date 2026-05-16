<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('consultas', function (Blueprint $table) {
            // Campo vital para el módulo de caja/ventas
            $table->boolean('pagado')->default(false)->after('fecha_atencion');
        
            // Opcional: Si quieres rastrear qué venta pagó esta consulta después
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn(['pagado', 'venta_id']);
        });
    }
};
