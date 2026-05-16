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
        // Añadir a la tabla servicios
        Schema::table('servicios', function (Blueprint $table) {
            $table->string('url_externa')->nullable()->after('descripcion');
        });

        // Añadir a la tabla negocios
        Schema::table('negocios', function (Blueprint $table) {
            $table->string('url_externa')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('url_externa');
        });

        Schema::table('negocios', function (Blueprint $table) {
            $table->dropColumn('url_externa');
        });
    }
};
