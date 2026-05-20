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
        Schema::create('tipo_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperativa_id')->nullable()->constrained('cooperativas')->cascadeOnDelete();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('recargo', 8, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_asientos');
    }
};
