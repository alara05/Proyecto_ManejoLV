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
        Schema::create('frecuencia_paradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('frecuencia_id')->constrained('frecuencias')->cascadeOnDelete();
            $table->foreignId('ciudad_id')->constrained('ciudades')->restrictOnDelete();
            $table->unsignedInteger('orden');
            $table->unsignedInteger('minutos_desde_origen')->default(0);
            $table->timestamps();

            $table->unique(['frecuencia_id', 'ciudad_id']);
            $table->unique(['frecuencia_id', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frecuencia_paradas');
    }
};
