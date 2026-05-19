<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use App\Models\Provincia;
use App\Models\Salida;
use App\Models\TipoAsiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoletoSeatSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_no_permite_vender_asiento_ocupado_en_la_misma_salida(): void
    {
        $user = User::factory()->create(['role' => 'oficinista']);
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Central']);
        $provincia = Provincia::create(['nombre' => 'Tungurahua']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '01',
            'placa' => 'ABC1234',
            'capacidad_total' => 40,
            'activo' => true,
        ]);
        $tipoAsiento = TipoAsiento::create([
            'cooperativa_id' => $cooperativa->id,
            'nombre' => 'Normal',
            'recargo' => 0,
            'activo' => true,
        ]);
        $asiento = Asiento::create([
            'bus_id' => $bus->id,
            'tipo_asiento_id' => $tipoAsiento->id,
            'numero' => 'A1',
            'activo' => true,
        ]);
        $frecuencia = Frecuencia::create([
            'cooperativa_id' => $cooperativa->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'hora_salida' => '08:00',
            'activa' => true,
        ]);
        $salida = Salida::create([
            'frecuencia_id' => $frecuencia->id,
            'bus_id' => $bus->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_salida' => '08:00',
            'estado' => 'programada',
            'precio_base' => 5,
            'generada_automaticamente' => false,
        ]);

        Boleto::create([
            'salida_id' => $salida->id,
            'user_id' => $user->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'codigo' => 'BOL-TEST-001',
            'pasajero_nombre' => 'Pasajero Uno',
            'pasajero_cedula' => '0102030405',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 5,
            'estado' => 'pagado',
            'vendido_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('boletos.store'), [
                'salida_id' => $salida->id,
                'tipo_asiento_id' => $tipoAsiento->id,
                'asiento_id' => $asiento->id,
                'pasajero_nombre' => 'Pasajero Dos',
                'pasajero_cedula' => '1102030405',
                'tipo_descuento' => 'ninguno',
            ])
            ->assertSessionHasErrors('asiento_id');

        $this->assertDatabaseMissing('boletos', [
            'salida_id' => $salida->id,
            'asiento_id' => $asiento->id,
            'pasajero_nombre' => 'Pasajero Dos',
        ]);
    }
}
