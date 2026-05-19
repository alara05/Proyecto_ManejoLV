<?php

use App\Models\Provincia;
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
        if (! Schema::hasTable('ciudades') || ! Schema::hasTable('provincias')) {
            return;
        }

        if (! Schema::hasColumn('ciudades', 'provincia_id')) {
            Schema::table('ciudades', function (Blueprint $table) {
                $table->foreignId('provincia_id')->nullable()->after('id')->constrained('provincias')->restrictOnDelete();
            });
        }

        if (Schema::hasColumn('ciudades', 'provincia')) {
            DB::table('ciudades')
                ->select('provincia')
                ->distinct()
                ->orderBy('provincia')
                ->get()
                ->each(function (object $ciudad): void {
                    Provincia::firstOrCreate(['nombre' => $ciudad->provincia]);
                });

            DB::table('ciudades')->orderBy('id')->get()->each(function (object $ciudad): void {
                $provincia = Provincia::where('nombre', $ciudad->provincia)->first();

                if ($provincia) {
                    DB::table('ciudades')
                        ->where('id', $ciudad->id)
                        ->update(['provincia_id' => $provincia->id]);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('ciudades') || ! Schema::hasColumn('ciudades', 'provincia_id')) {
            return;
        }

        Schema::table('ciudades', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provincia_id');
        });
    }
};
