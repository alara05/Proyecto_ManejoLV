<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use App\Models\Pago;
use App\Models\Provincia;
use App\Models\Salida;
use App\Models\TipoAsiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagoComprobanteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_cliente_carga_comprobante_y_pago_queda_pendiente(): void
    {
        Storage::fake('public');
        $boleto = $this->crearBoletoReservado();

        $this->post(route('cliente.pagos.store', $boleto), [
            'metodo' => 'transferencia',
            'monto' => 12,
            'comprobante' => UploadedFile::fake()->image('comprobante.jpg'),
            'observacion' => 'Operacion 123',
        ])->assertRedirect(route('cliente.pagos.create', $boleto));

        $pago = Pago::firstOrFail();

        $this->assertSame('pendiente', $pago->estado);
        $this->assertSame('transferencia', $pago->metodo);
        $this->assertSame('Operacion 123', $pago->observacion);
        Storage::disk('public')->assertExists($pago->comprobante_path);
    }

    public function test_pago_pendiente_puede_validarse_manualmente(): void
    {
        $validador = User::factory()->create();
        $boleto = $this->crearBoletoReservado();
        $pago = Pago::create([
            'boleto_id' => $boleto->id,
            'metodo' => 'deposito',
            'monto' => 12,
            'comprobante_path' => 'comprobantes/demo.pdf',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($validador)
            ->patch(route('pagos.validar', $pago))
            ->assertRedirect(route('pagos.show', $pago));

        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'estado' => 'validado',
            'validado_por' => $validador->id,
        ]);

        $this->assertDatabaseHas('boletos', [
            'id' => $boleto->id,
            'estado' => 'pagado',
        ]);
    }

    private function crearBoletoReservado(): Boleto
    {
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Pago']);
        $provincia = Provincia::create(['nombre' => 'Pichincha']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '11',
            'placa' => 'PAG1234',
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
            'hora_salida' => '09:00',
            'activa' => true,
        ]);
        $salida = Salida::create([
            'frecuencia_id' => $frecuencia->id,
            'bus_id' => $bus->id,
            'fecha' => now()->addDay()->toDateString(),
            'hora_salida' => '09:00',
            'estado' => 'programada',
            'precio_base' => 12,
            'generada_automaticamente' => false,
        ]);

        return Boleto::create([
            'salida_id' => $salida->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'codigo' => 'BOL-PAGO-' . uniqid(),
            'pasajero_nombre' => 'Cliente Pago',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 12,
            'estado' => 'reservado',
        ]);
    }
}
