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
        Schema::create('cooperativas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('ruc', 13)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperativas');
    }
};
