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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salida_id')->constrained('salidas')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asiento_id')->constrained('asientos')->restrictOnDelete();
            $table->foreignId('ciudad_origen_id')->constrained('ciudades')->restrictOnDelete();
            $table->foreignId('ciudad_destino_id')->constrained('ciudades')->restrictOnDelete();
            $table->string('codigo')->unique();
            $table->string('pasajero_nombre');
            $table->string('pasajero_cedula', 10);
            $table->enum('tipo_descuento', ['ninguno', 'menor_edad', 'discapacidad', 'tercera_edad'])->default('ninguno');
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->decimal('precio', 8, 2);
            $table->enum('estado', ['reservado', 'pagado', 'anulado', 'usado'])->default('reservado');
            $table->timestamp('vendido_at')->nullable();
            $table->timestamps();

            $table->unique(['salida_id', 'asiento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
