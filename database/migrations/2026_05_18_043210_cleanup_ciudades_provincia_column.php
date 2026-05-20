<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('ciudades') || ! Schema::hasColumn('ciudades', 'provincia_id')) {
            return;
        }

        if (! $this->indexExists('ciudades', 'ciudades_nombre_provincia_id_unique')) {
            Schema::table('ciudades', function (Blueprint $table) {
                $table->unique(['nombre', 'provincia_id']);
            });
        }

        if (Schema::hasColumn('ciudades', 'provincia')) {
            if ($this->indexExists('ciudades', 'ciudades_nombre_provincia_unique')) {
                Schema::table('ciudades', function (Blueprint $table) {
                    $table->dropUnique('ciudades_nombre_provincia_unique');
                });
            }

            Schema::table('ciudades', function (Blueprint $table) {
                $table->dropColumn('provincia');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('ciudades') || Schema::hasColumn('ciudades', 'provincia')) {
            return;
        }

        Schema::table('ciudades', function (Blueprint $table) {
            $table->string('provincia')->nullable()->after('nombre');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return true;
        }

        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('database()'))
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
