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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperativa_id')->constrained('cooperativas')->cascadeOnDelete();
            $table->string('numero');
            $table->string('placa', 10)->unique();
            $table->string('marca_chasis')->nullable();
            $table->string('marca_carroceria')->nullable();
            $table->unsignedSmallInteger('anio')->nullable();
            $table->unsignedSmallInteger('capacidad_total')->default(0);
            $table->string('foto_path')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['cooperativa_id', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
