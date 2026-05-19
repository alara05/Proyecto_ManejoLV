<?php

namespace Tests\Feature;

use App\Models\ConfiguracionAplicacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConfiguracionAplicacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('El driver pdo_sqlite no esta disponible en este entorno.');
        }

        parent::setUp();
    }

    public function test_admin_actualiza_configuracion_de_aplicacion(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $logo = UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml');

        $this->actingAs($admin)
            ->put(route('configuracion.update'), [
                'nombre_aplicacion' => 'Pasajes Ecuador',
                'logo' => $logo,
                'color_primario' => '#123456',
                'color_secundario' => '#abcdef',
                'email_soporte' => 'soporte@example.com',
                'telefono_soporte' => '0999999999',
                'redes_sociales' => [
                    'facebook' => 'https://facebook.com/pasajes',
                    'instagram' => 'https://instagram.com/pasajes',
                ],
            ])
            ->assertRedirect(route('configuracion.edit'));

        $configuracion = ConfiguracionAplicacion::firstOrFail();

        $this->assertSame('Pasajes Ecuador', $configuracion->nombre_aplicacion);
        $this->assertSame('#123456', $configuracion->color_primario);
        $this->assertSame('#abcdef', $configuracion->color_secundario);
        $this->assertSame('soporte@example.com', $configuracion->email_soporte);
        $this->assertSame('0999999999', $configuracion->telefono_soporte);
        $this->assertSame('https://facebook.com/pasajes', $configuracion->redes_sociales['facebook']);
        Storage::disk('public')->assertExists($configuracion->logo_path);
    }

    public function test_cliente_no_puede_actualizar_configuracion(): void
    {
        $cliente = User::factory()->create(['role' => 'cliente']);

        $this->actingAs($cliente)
            ->put(route('configuracion.update'), [
                'nombre_aplicacion' => 'Intento Cliente',
                'color_primario' => '#123456',
                'color_secundario' => '#abcdef',
            ])
            ->assertForbidden();
    }
}
