<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use App\Models\Provincia;
use App\Models\RegistroAcceso;
use App\Models\Salida;
use App\Models\TipoAsiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoPasajeroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_personal_bus_registra_acceso_permitido_para_boleto_pagado(): void
    {
        $personal = User::factory()->create(['role' => 'personal_bus']);
        $boleto = $this->crearBoleto('pagado');

        $this->actingAs($personal)
            ->post(route('accesos.store'), [
                'codigo' => $boleto->codigo,
            ])
            ->assertRedirect(route('accesos.index'));

        $this->assertDatabaseHas('registro_accesos', [
            'boleto_id' => $boleto->id,
            'registrado_por' => $personal->id,
            'resultado' => 'permitido',
        ]);

        $this->assertDatabaseHas('boletos', [
            'id' => $boleto->id,
            'estado' => 'usado',
        ]);
    }

    public function test_personal_bus_rechaza_boleto_no_pagado(): void
    {
        $personal = User::factory()->create(['role' => 'personal_bus']);
        $boleto = $this->crearBoleto('reservado');

        $this->actingAs($personal)
            ->post(route('accesos.store'), [
                'codigo' => $boleto->codigo,
            ])
            ->assertRedirect(route('accesos.index'));

        $this->assertDatabaseHas('registro_accesos', [
            'boleto_id' => $boleto->id,
            'registrado_por' => $personal->id,
            'resultado' => 'rechazado',
        ]);

        $this->assertDatabaseHas('boletos', [
            'id' => $boleto->id,
            'estado' => 'reservado',
        ]);
    }

    public function test_cliente_no_puede_registrar_accesos(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);
        $boleto = $this->crearBoleto('pagado');

        $this->actingAs($cliente)
            ->post(route('accesos.store'), [
                'codigo' => $boleto->codigo,
            ])
            ->assertForbidden();

        $this->assertSame(0, RegistroAcceso::count());
    }

    private function crearBoleto(string $estado): Boleto
    {
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Acceso']);
        $provincia = Provincia::create(['nombre' => 'Tungurahua']);
        $origen = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Ambato']);
        $destino = Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'Quito']);
        $bus = Bus::create([
            'cooperativa_id' => $cooperativa->id,
            'numero' => '22',
            'placa' => 'ACC1234',
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
            'precio_base' => 10,
            'generada_automaticamente' => false,
        ]);

        return Boleto::create([
            'salida_id' => $salida->id,
            'asiento_id' => $asiento->id,
            'ciudad_origen_id' => $origen->id,
            'ciudad_destino_id' => $destino->id,
            'codigo' => 'BOL-ACCESO-' . uniqid(),
            'pasajero_nombre' => 'Pasajero Acceso',
            'pasajero_cedula' => '1712345678',
            'tipo_descuento' => 'ninguno',
            'porcentaje_descuento' => 0,
            'precio' => 10,
            'estado' => $estado,
            'vendido_at' => $estado === 'pagado' ? now() : null,
        ]);
    }
}
