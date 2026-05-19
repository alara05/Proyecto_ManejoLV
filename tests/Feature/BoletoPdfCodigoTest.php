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

class BoletoPdfCodigoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_boleto_pdf_es_descargable_despues_de_compra(): void
    {
        $boleto = $this->crearBoleto();

        $this->get(route('cliente.boletos.pdf', $boleto))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_boleto_aparece_en_historial_del_cliente(): void
    {
        $user = User::factory()->create();
        $boleto = $this->crearBoleto(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('cliente.boletos.historial'))
            ->assertOk()
            ->assertSee($boleto->codigo)
            ->assertSee('PDF');
    }

    private function crearBoleto(array $extra = []): Boleto
    {
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa PDF']);
        $provincia = Provincia::create(['nombre' => 'Pichincha']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '21',
            'placa' => 'PDF1234',
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
            'precio_base' => 8,
            'generada_automaticamente' => false,
        ]);

        return Boleto::create(array_merge([
            'salida_id' => $salida->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'codigo' => 'BOL-PDF-001',
            'pasajero_nombre' => 'Cliente PDF',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 8,
            'estado' => 'reservado',
        ], $extra));
    }
}
