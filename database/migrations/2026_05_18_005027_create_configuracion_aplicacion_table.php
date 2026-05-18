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
        Schema::create('configuracion_aplicacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_aplicacion')->default('Manejo Buses');
            $table->string('logo_path')->nullable();
            $table->string('color_primario', 20)->default('#0f172a');
            $table->string('color_secundario', 20)->default('#f59e0b');
            $table->string('email_soporte')->nullable();
            $table->string('telefono_soporte', 20)->nullable();
            $table->json('redes_sociales')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_aplicacion');
    }
};
