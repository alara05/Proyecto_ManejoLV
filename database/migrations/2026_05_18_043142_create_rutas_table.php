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
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperativa_id')->constrained('cooperativas')->cascadeOnDelete();
            $table->foreignId('bus_id')->nullable()->constrained('buses')->nullOnDelete();
            $table->foreignId('ciudad_origen_id')->constrained('ciudades')->restrictOnDelete();
            $table->foreignId('ciudad_destino_id')->constrained('ciudades')->restrictOnDelete();
            $table->string('nombre');
            $table->enum('tipo_viaje', ['directo', 'con_paradas'])->default('directo');
            $table->decimal('distancia_km', 8, 2)->nullable();
            $table->unsignedSmallInteger('duracion_minutos')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->unique(
                ['cooperativa_id', 'ciudad_origen_id', 'ciudad_destino_id', 'nombre'],
                'rutas_coop_origen_destino_nombre_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
