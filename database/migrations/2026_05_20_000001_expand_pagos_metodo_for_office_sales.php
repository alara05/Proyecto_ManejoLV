<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pagos MODIFY metodo ENUM('efectivo', 'tarjeta', 'transferencia', 'deposito') NOT NULL DEFAULT 'efectivo'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('pagos')
                ->whereIn('metodo', ['efectivo', 'tarjeta'])
                ->update(['metodo' => 'transferencia']);

            DB::statement("ALTER TABLE pagos MODIFY metodo ENUM('transferencia', 'deposito') NOT NULL DEFAULT 'transferencia'");
        }
    }
};
