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
use App\Notifications\BoletoReservadoNotification;
use App\Notifications\ComprobantePagoCargadoNotification;
use App\Notifications\PagoRechazadoNotification;
use App\Notifications\PagoValidadoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificacionProcesoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_cliente_recibe_notificacion_al_reservar_boleto(): void
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
                'tipo_descuento' => 'ninguno',
            ])
            ->assertRedirect();

        Notification::assertSentTo($cliente, BoletoReservadoNotification::class);
    }

    public function test_admin_y_oficinista_reciben_notificacion_al_cargar_comprobante(): void
    {
        Notification::fake();
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $oficinista = User::factory()->create(['role' => 'oficinista']);
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boleto = $this->crearBoletoReservado($cliente);

        $this->actingAs($cliente)
            ->post(route('cliente.pagos.store', $boleto), [
                'metodo' => 'transferencia',
                'monto' => 12,
                'comprobante' => UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('cliente.pagos.create', $boleto));

        Notification::assertSentTo($admin, ComprobantePagoCargadoNotification::class);
        Notification::assertSentTo($oficinista, ComprobantePagoCargadoNotification::class);
        Notification::assertNotSentTo($cliente, ComprobantePagoCargadoNotification::class);
    }

    public function test_cliente_recibe_notificacion_cuando_pago_se_valida_o_rechaza(): void
    {
        Notification::fake();
        $validador = User::factory()->create(['role' => 'oficinista']);
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boletoValidado = $this->crearBoletoReservado($cliente);
        $pagoValidado = $this->crearPagoPendiente($boletoValidado);

        $this->actingAs($validador)
            ->patch(route('pagos.validar', $pagoValidado))
            ->assertRedirect(route('pagos.show', $pagoValidado));

        Notification::assertSentTo($cliente, PagoValidadoNotification::class);

        $boletoRechazado = $this->crearBoletoReservado($cliente);
        $pagoRechazado = $this->crearPagoPendiente($boletoRechazado);

        $this->actingAs($validador)
            ->patch(route('pagos.rechazar', $pagoRechazado), [
                'observacion' => 'Comprobante ilegible.',
            ])
            ->assertRedirect(route('pagos.show', $pagoRechazado));

        Notification::assertSentTo($cliente, PagoRechazadoNotification::class);
    }

    public function test_usuario_puede_ver_y_marcar_notificacion_como_leida(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boleto = $this->crearBoletoReservado($cliente);

        $cliente->notify(new BoletoReservadoNotification($boleto));
        $notificacion = $cliente->notifications()->firstOrFail();

        $this->actingAs($cliente)
            ->get(route('notificaciones.index'))
            ->assertOk()
            ->assertSee('Boleto reservado');

        $this->actingAs($cliente)
            ->patch(route('notificaciones.leer', $notificacion->id))
            ->assertRedirect(route('cliente.boletos.show', $boleto));

        $this->assertNotNull($notificacion->fresh()->read_at);
    }

    public function test_usuario_puede_descargar_notificaciones_en_pdf(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boleto = $this->crearBoletoReservado($cliente);

        $cliente->notify(new BoletoReservadoNotification($boleto));

        $this->actingAs($cliente)
            ->get(route('notificaciones.pdf'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    private function crearSalidaConAsiento(): array
    {
        $sufijo = substr(uniqid(), -6);
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Notificaciones ' . $sufijo]);
        $provincia = Provincia::create(['nombre' => 'Tungurahua ' . $sufijo]);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => $sufijo,
            'placa' => strtoupper($sufijo) . 'N',
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
            'precio_base' => 12,
            'generada_automaticamente' => false,
        ]);

        return [$salida, $asiento, $tipoAsiento, $origen, $destino];
    }

    private function crearBoletoReservado(User $cliente): Boleto
    {
        [$salida, $asiento, , $origen, $destino] = $this->crearSalidaConAsiento();

        return Boleto::create([
            'salida_id' => $salida->id,
            'user_id' => $cliente->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'codigo' => 'BOL-NOT-' . uniqid(),
            'pasajero_nombre' => 'Cliente Notificado',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 12,
            'estado' => 'reservado',
        ]);
    }

    private function crearPagoPendiente(Boleto $boleto): Pago
    {
        return Pago::create([
            'boleto_id' => $boleto->id,
            'metodo' => 'deposito',
            'monto' => 12,
            'comprobante_path' => 'comprobantes/demo.pdf',
            'estado' => 'pendiente',
        ]);
    }
}
