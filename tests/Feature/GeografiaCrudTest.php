<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\Provincia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class GeografiaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_puede_gestionar_provincias(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('provincias.store'), [
                'nombre' => 'Tungurahua',
                'activa' => '1',
            ])
            ->assertRedirect(route('provincias.index'));

        $provincia = Provincia::where('nombre', 'Tungurahua')->firstOrFail();

        $this->actingAs($user)
            ->get(route('provincias.index'))
            ->assertOk()
            ->assertSee('Tungurahua');

        $this->actingAs($user)
            ->put(route('provincias.update', $provincia), [
                'nombre' => 'Cotopaxi',
                'activa' => '1',
            ])
            ->assertRedirect(route('provincias.index'));

        $this->assertDatabaseHas('provincias', ['nombre' => 'Cotopaxi']);

        $this->actingAs($user)
            ->delete(route('provincias.destroy', $provincia))
            ->assertRedirect(route('provincias.index'));

        $this->assertDatabaseMissing('provincias', ['nombre' => 'Cotopaxi']);
    }

    public function test_usuario_autenticado_puede_gestionar_ciudades(): void
    {
        $user = User::factory()->create();
        $provincia = Provincia::create([
            'nombre' => 'Tungurahua',
            'activa' => true,
        ]);

        $this->actingAs($user)
            ->post(route('ciudades.store'), [
                'provincia_id' => $provincia->id,
                'nombre' => 'Ambato',
                'activa' => '1',
            ])
            ->assertRedirect(route('ciudades.index'));

        $ciudad = Ciudad::where('nombre', 'Ambato')->firstOrFail();

        $this->actingAs($user)
            ->get(route('ciudades.index'))
            ->assertOk()
            ->assertSee('Ambato')
            ->assertSee('Tungurahua');

        $this->actingAs($user)
            ->put(route('ciudades.update', $ciudad), [
                'provincia_id' => $provincia->id,
                'nombre' => 'Banos',
                'activa' => '1',
            ])
            ->assertRedirect(route('ciudades.index'));

        $this->assertDatabaseHas('ciudades', [
            'provincia_id' => $provincia->id,
            'nombre' => 'Banos',
        ]);

        $this->actingAs($user)
            ->delete(route('ciudades.destroy', $ciudad))
            ->assertRedirect(route('ciudades.index'));

        $this->assertDatabaseMissing('ciudades', ['nombre' => 'Banos']);
    }

    public function test_no_elimina_provincia_con_ciudades_asociadas(): void
    {
        $user = User::factory()->create();
        $provincia = Provincia::create([
            'nombre' => 'Pichincha',
            'activa' => true,
        ]);
        Ciudad::create([
            'provincia_id' => $provincia->id,
            'nombre' => 'Quito',
            'activa' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('provincias.destroy', $provincia))
            ->assertRedirect(route('provincias.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('provincias', ['nombre' => 'Pichincha']);
    }
}
