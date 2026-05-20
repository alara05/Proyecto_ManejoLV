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
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frecuencia_id')->constrained('frecuencias')->restrictOnDelete();
            $table->foreignId('bus_id')->constrained('buses')->restrictOnDelete();
            $table->date('fecha');
            $table->time('hora_salida');
            $table->enum('estado', ['programada', 'en_ruta', 'finalizada', 'cancelada'])->default('programada');
            $table->decimal('precio_base', 8, 2);
            $table->boolean('generada_automaticamente')->default(false);
            $table->timestamps();

            $table->unique(['bus_id', 'fecha', 'hora_salida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
