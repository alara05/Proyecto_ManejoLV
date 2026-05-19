<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\Bus;
use App\Models\Cooperativa;
use App\Models\TipoAsiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsientoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_tipo_asientos_index_is_available_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Central']);

        TipoAsiento::create([
            'cooperativa_id' => $cooperativa->id,
            'nombre' => 'Ejecutivo',
            'recargo' => 5,
            'activo' => true,
        ]);

        $this->actingAs($user)
            ->get(route('tipo-asientos.index'))
            ->assertOk()
            ->assertSee('Tipos de asientos')
            ->assertSee('Ejecutivo');
    }

    public function test_asientos_index_is_available_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $cooperativa = Cooperativa::create(['nombre' => 'Cooperativa Norte']);
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

        Asiento::create([
            'bus_id' => $bus->id,
            'tipo_asiento_id' => $tipoAsiento->id,
            'numero' => 'A1',
            'activo' => true,
        ]);

        $this->actingAs($user)
            ->get(route('asientos.index'))
            ->assertOk()
            ->assertSee('Asientos')
            ->assertSee('A1');
    }

    public function test_asiento_type_must_match_bus_cooperativa_when_type_is_scoped(): void
    {
        $user = User::factory()->create();
        $busCooperativa = Cooperativa::create(['nombre' => 'Cooperativa Sur']);
        $otraCooperativa = Cooperativa::create(['nombre' => 'Cooperativa Este']);
        $bus = Bus::create([
            'cooperativa_id' => $busCooperativa->id,
            'numero' => '09',
            'placa' => 'XYZ4321',
            'capacidad_total' => 36,
            'activo' => true,
        ]);
        $tipoAsiento = TipoAsiento::create([
            'cooperativa_id' => $otraCooperativa->id,
            'nombre' => 'VIP',
            'recargo' => 10,
            'activo' => true,
        ]);

        $this->actingAs($user)
            ->post(route('asientos.store'), [
                'bus_id' => $bus->id,
                'tipo_asiento_id' => $tipoAsiento->id,
                'numero' => 'B2',
                'activo' => '1',
            ])
            ->assertSessionHasErrors('tipo_asiento_id');

        $this->assertDatabaseMissing('asientos', [
            'bus_id' => $bus->id,
            'tipo_asiento_id' => $tipoAsiento->id,
            'numero' => 'B2',
        ]);
    }
}
