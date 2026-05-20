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
        Schema::create('frecuencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperativa_id')->constrained('cooperativas')->cascadeOnDelete();
            $table->foreignId('ciudad_origen_id')->constrained('ciudades')->restrictOnDelete();
            $table->foreignId('ciudad_destino_id')->constrained('ciudades')->restrictOnDelete();
            $table->time('hora_salida');
            $table->string('numero_resolucion_ant')->nullable();
            $table->date('fecha_resolucion_ant')->nullable();
            $table->boolean('tiene_paradas')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frecuencias');
    }
};
