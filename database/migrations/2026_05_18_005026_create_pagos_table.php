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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boleto_id')->constrained('boletos')->cascadeOnDelete();
            $table->foreignId('validado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('metodo', ['transferencia', 'deposito'])->default('transferencia');
            $table->decimal('monto', 8, 2);
            $table->string('comprobante_path')->nullable();
            $table->enum('estado', ['pendiente', 'validado', 'rechazado'])->default('pendiente');
            $table->timestamp('validado_at')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
