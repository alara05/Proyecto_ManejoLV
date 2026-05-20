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
use App\Notifications\BoletoEmitidoNotification;
use App\Notifications\CompraRegistradaNotification;
use App\Notifications\PagoPendienteNotification;
use App\Notifications\PagoValidadoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificacionCorreoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_compra_registrada_notifica_al_cliente(): void
    {
        Notification::fake();

        $cliente = User::factory()->create(['role' => 'cliente']);
        [$salida, $asiento, $tipoAsiento] = $this->crearSalidaConAsiento();

        $this->actingAs($cliente)
            ->post(route('cliente.boletos.store'), [
                'salida_id' => $salida->id,
                'tipo_asiento_id' => $tipoAsiento->id,
                'asiento_id' => $asiento->id,
                'pasajero_nombre' => 'Cliente Notificado',
                'pasajero_cedula' => '1712345678',
                'cliente_email' => $cliente->email,
                'tipo_descuento' => 'ninguno',
                'metodo_pago' => 'transferencia',
                'comprobante_tipo' => 'ticket',
                'transferencia_banco' => 'Banco Pichincha',
                'transferencia_referencia' => 'TRX-002',
                'transferencia_titular' => 'Cliente Notificado',
            ])
            ->assertRedirect();

        Notification::assertSentTo($cliente, CompraRegistradaNotification::class);
    }

    public function test_cliente_puede_descargar_notificaciones_en_pdf(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boleto = $this->crearBoletoReservado($cliente);

        $cliente->notify(new CompraRegistradaNotification($boleto));

        $this->actingAs($cliente)
            ->get(route('notificaciones.pdf'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pago_pendiente_validado_y_boleto_emitido_notifican(): void
    {
        Notification::fake();
        Storage::fake('public');

        $cliente = User::factory()->create(['role' => 'cliente']);
        $validador = User::factory()->create(['role' => 'oficinista']);
        $boleto = $this->crearBoletoReservado($cliente);

        $this->post(route('cliente.pagos.store', $boleto), [
            'metodo' => 'transferencia',
            'monto' => 12,
            'comprobante' => UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf'),
            'observacion' => 'Operacion 123',
        ])->assertRedirect(route('cliente.pagos.create', $boleto));

        Notification::assertSentTo($cliente, PagoPendienteNotification::class);

        $pago = Pago::firstOrFail();

        $this->actingAs($validador)
            ->patch(route('pagos.validar', $pago))
            ->assertRedirect(route('pagos.show', $pago));

        Notification::assertSentTo($cliente, PagoValidadoNotification::class);
        Notification::assertSentTo($cliente, BoletoEmitidoNotification::class);
    }

    private function crearBoletoReservado(User $cliente): Boleto
    {
        [$salida, $asiento] = $this->crearSalidaConAsiento();

        return Boleto::create([
            'salida_id' => $salida->id,
            'user_id' => $cliente->id,
            'cliente_email' => $cliente->email,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $salida->frecuencia->ciudad_origen_id,
            'ciudad_destino_id' => $salida->frecuencia->ciudad_destino_id,
            'codigo' => 'BOL-NOT-'.uniqid(),
            'pasajero_nombre' => 'Cliente Notificado',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 12,
            'estado' => 'reservado',
        ]);
    }

    private function crearSalidaConAsiento(): array
    {
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Notifica']);
        $provincia = Provincia::create(['nombre' => 'Pichincha']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '22',
            'placa' => 'NOT1234',
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

        return [$salida, $asiento, $tipoAsiento];
    }
}
