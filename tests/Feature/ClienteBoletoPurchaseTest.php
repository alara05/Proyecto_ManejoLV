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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteBoletoPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_cliente_registra_boleto_con_descuento_y_datos_del_viaje(): void
    {
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Cliente']);
        $provincia = Provincia::create(['nombre' => 'Pichincha']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '12',
            'placa' => 'PBC1234',
            'capacidad_total' => 42,
            'activo' => true,
        ]);
        $tipoAsiento = TipoAsiento::create([
            'cooperativa_id' => $cooperativa->id,
            'nombre' => 'Ejecutivo',
            'recargo' => 2,
            'activo' => true,
        ]);
        $asiento = Asiento::create([
            'bus_id' => $bus->id,
            'tipo_asiento_id' => $tipoAsiento->id,
            'numero' => 'C4',
            'activo' => true,
        ]);
        $frecuencia = Frecuencia::create([
            'cooperativa_id' => $cooperativa->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'hora_salida' => '10:30',
            'activa' => true,
        ]);
        $salida = Salida::create([
            'frecuencia_id' => $frecuencia->id,
            'bus_id' => $bus->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_salida' => '10:30',
            'estado' => 'programada',
            'precio_base' => 10,
            'generada_automaticamente' => false,
        ]);

        $this->post(route('cliente.boletos.store'), [
            'salida_id' => $salida->id,
            'tipo_asiento_id' => $tipoAsiento->id,
            'asiento_id' => $asiento->id,
            'pasajero_nombre' => 'Cliente Final',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'tercera_edad',
        ])->assertRedirect();

        $this->assertDatabaseHas('boletos', [
            'salida_id' => $salida->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'pasajero_nombre' => 'Cliente Final',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'tercera_edad',
            'porcentaje_descuento' => 50,
            'precio' => 6,
            'estado' => 'reservado',
        ]);

        $this->assertSame(1, Boleto::count());
    }
}
