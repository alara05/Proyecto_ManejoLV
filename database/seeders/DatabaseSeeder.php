<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Boleto;
use App\Models\Bus;
use App\Models\Ciudad;
use App\Models\ConfiguracionAplicacion;
use App\Models\Cooperativa;
use App\Models\Frecuencia;
use App\Models\FrecuenciaParada;
use App\Models\Pago;
use App\Models\Provincia;
use App\Models\RegistroAcceso;
use App\Models\Ruta;
use App\Models\Salida;
use App\Models\TipoAsiento;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        /*
         |--------------------------------------------------------------------------
         | Usuario administrador unico para pruebas
         |--------------------------------------------------------------------------
         */
        $admin = User::updateOrCreate(
            ['email' => 'Admin@gmail.com'],
            [
                'name' => 'Administrador General',
                'cedula' => '9999999999',
                'password' => '12345678AD',
                'role' => 'admin',
                'cooperativa_id' => null,
                'telefono' => '0999999999',
                'activo' => true,
            ]
        );

        $admin->forceFill([
            'email_verified_at' => $now,
        ])->save();

        /*
         |--------------------------------------------------------------------------
         | Configuracion general de la aplicacion
         |--------------------------------------------------------------------------
         */
        ConfiguracionAplicacion::updateOrCreate(
            ['id' => 1],
            [
                'nombre_aplicacion' => 'Manejo Buses',
                'logo_path' => null,
                'color_primario' => '#24a8ff',
                'color_secundario' => '#ec7519',
                'email_soporte' => 'soporte@manejolbuses.com',
                'telefono_soporte' => '0999999999',
                'redes_sociales' => [
                    'facebook' => 'Manejo Buses',
                    'instagram' => '@manejabuses',
                ],
            ]
        );

        /*
         |--------------------------------------------------------------------------
         | 5 provincias
         |--------------------------------------------------------------------------
         */
        $provinciasData = [
            ['nombre' => 'Pichincha', 'activa' => true],
            ['nombre' => 'Guayas', 'activa' => true],
            ['nombre' => 'Manabi', 'activa' => true],
            ['nombre' => 'Azuay', 'activa' => true],
            ['nombre' => 'Tungurahua', 'activa' => true],
        ];

        $provincias = collect($provinciasData)->map(fn (array $data) => Provincia::updateOrCreate(
            ['nombre' => $data['nombre']],
            $data
        ))->values();

        /*
         |--------------------------------------------------------------------------
         | 5 ciudades
         |--------------------------------------------------------------------------
         */
        $ciudadesData = [
            ['nombre' => 'Quito', 'provincia' => 'Pichincha'],
            ['nombre' => 'Guayaquil', 'provincia' => 'Guayas'],
            ['nombre' => 'Manta', 'provincia' => 'Manabi'],
            ['nombre' => 'Cuenca', 'provincia' => 'Azuay'],
            ['nombre' => 'Ambato', 'provincia' => 'Tungurahua'],
        ];

        $ciudades = collect($ciudadesData)->map(function (array $data) use ($provincias) {
            $provincia = $provincias->firstWhere('nombre', $data['provincia']);

            return Ciudad::updateOrCreate(
                [
                    'nombre' => $data['nombre'],
                    'provincia_id' => $provincia->id,
                ],
                [
                    'activa' => true,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 cooperativas
         |--------------------------------------------------------------------------
         */
        $cooperativasData = [
            [
                'nombre' => 'Cooperativa Andina Express',
                'ruc' => '1790010001001',
                'telefono' => '022100001',
                'email' => 'andina@example.com',
                'direccion' => 'Terminal Terrestre Quito',
            ],
            [
                'nombre' => 'Cooperativa Costa Azul',
                'ruc' => '0990010001001',
                'telefono' => '042100001',
                'email' => 'costa@example.com',
                'direccion' => 'Terminal Terrestre Guayaquil',
            ],
            [
                'nombre' => 'Cooperativa Manabi Tours',
                'ruc' => '1390010001001',
                'telefono' => '052100001',
                'email' => 'manabi@example.com',
                'direccion' => 'Terminal Terrestre Manta',
            ],
            [
                'nombre' => 'Cooperativa Austro Viajes',
                'ruc' => '0190010001001',
                'telefono' => '072100001',
                'email' => 'austro@example.com',
                'direccion' => 'Terminal Terrestre Cuenca',
            ],
            [
                'nombre' => 'Cooperativa Sierra Centro',
                'ruc' => '1890010001001',
                'telefono' => '032100001',
                'email' => 'sierra@example.com',
                'direccion' => 'Terminal Terrestre Ambato',
            ],
        ];

        $cooperativas = collect($cooperativasData)->map(fn (array $data) => Cooperativa::updateOrCreate(
            ['ruc' => $data['ruc']],
            $data + [
                'logo_path' => null,
                'activa' => true,
            ]
        ))->values();

        /*
         |--------------------------------------------------------------------------
         | 5 tipos de asiento
         |--------------------------------------------------------------------------
         */
        $tiposData = [
            ['nombre' => 'Estandar', 'descripcion' => 'Asiento regular para viaje nacional.', 'recargo' => 0.00],
            ['nombre' => 'Ejecutivo', 'descripcion' => 'Asiento amplio con mayor comodidad.', 'recargo' => 2.00],
            ['nombre' => 'VIP', 'descripcion' => 'Asiento preferencial con recargo premium.', 'recargo' => 4.00],
            ['nombre' => 'Ventana', 'descripcion' => 'Asiento junto a ventana.', 'recargo' => 1.00],
            ['nombre' => 'Pasillo', 'descripcion' => 'Asiento junto al pasillo.', 'recargo' => 0.50],
        ];

        $tiposAsiento = collect($tiposData)->map(function (array $data, int $index) use ($cooperativas) {
            return TipoAsiento::updateOrCreate(
                [
                    'cooperativa_id' => $cooperativas[$index]->id,
                    'nombre' => $data['nombre'],
                ],
                [
                    'descripcion' => $data['descripcion'],
                    'recargo' => $data['recargo'],
                    'activo' => true,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 buses
         |--------------------------------------------------------------------------
         */
        $busesData = [
            ['numero' => 'BUS-001', 'placa' => 'PBA1001', 'marca_chasis' => 'Mercedes Benz', 'marca_carroceria' => 'IMCE', 'anio' => 2021],
            ['numero' => 'BUS-002', 'placa' => 'GBA1002', 'marca_chasis' => 'Volkswagen', 'marca_carroceria' => 'Cepeda', 'anio' => 2020],
            ['numero' => 'BUS-003', 'placa' => 'MBA1003', 'marca_chasis' => 'Scania', 'marca_carroceria' => 'Miral', 'anio' => 2022],
            ['numero' => 'BUS-004', 'placa' => 'ABA1004', 'marca_chasis' => 'Volvo', 'marca_carroceria' => 'Varma', 'anio' => 2023],
            ['numero' => 'BUS-005', 'placa' => 'TBA1005', 'marca_chasis' => 'Hino', 'marca_carroceria' => 'Olmedo', 'anio' => 2021],
        ];

        $buses = collect($busesData)->map(function (array $data, int $index) use ($cooperativas) {
            return Bus::updateOrCreate(
                ['placa' => $data['placa']],
                $data + [
                    'cooperativa_id' => $cooperativas[$index]->id,
                    'capacidad_total' => 5,
                    'foto_path' => null,
                    'activo' => true,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 asientos por bus para que existan asientos disponibles en ventas
         |--------------------------------------------------------------------------
         */
        $asientosPorBus = collect();

        $buses->each(function (Bus $bus, int $busIndex) use ($tiposAsiento, $asientosPorBus) {
            for ($numero = 1; $numero <= 5; $numero++) {
                $asientosPorBus->push(Asiento::updateOrCreate(
                    [
                        'bus_id' => $bus->id,
                        'numero' => str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
                    ],
                    [
                        'tipo_asiento_id' => $tiposAsiento[$busIndex]->id,
                        'activo' => true,
                    ]
                ));
            }
        });

        /*
         |--------------------------------------------------------------------------
         | 5 frecuencias
         |--------------------------------------------------------------------------
         */
        $frecuenciasData = [
            ['origen' => 'Quito', 'destino' => 'Guayaquil', 'hora_salida' => '07:00:00'],
            ['origen' => 'Guayaquil', 'destino' => 'Manta', 'hora_salida' => '09:30:00'],
            ['origen' => 'Manta', 'destino' => 'Cuenca', 'hora_salida' => '12:00:00'],
            ['origen' => 'Cuenca', 'destino' => 'Ambato', 'hora_salida' => '15:30:00'],
            ['origen' => 'Ambato', 'destino' => 'Quito', 'hora_salida' => '18:00:00'],
        ];

        $frecuencias = collect($frecuenciasData)->map(function (array $data, int $index) use ($cooperativas, $ciudades, $now) {
            $origen = $ciudades->firstWhere('nombre', $data['origen']);
            $destino = $ciudades->firstWhere('nombre', $data['destino']);

            return Frecuencia::updateOrCreate(
                [
                    'cooperativa_id' => $cooperativas[$index]->id,
                    'ciudad_origen_id' => $origen->id,
                    'ciudad_destino_id' => $destino->id,
                    'hora_salida' => $data['hora_salida'],
                ],
                [
                    'numero_resolucion_ant' => 'ANT-2026-00'.($index + 1),
                    'fecha_resolucion_ant' => $now->copy()->subMonths($index + 1)->toDateString(),
                    'tiene_paradas' => true,
                    'activa' => true,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 paradas de frecuencia
         |--------------------------------------------------------------------------
         */
        $frecuencias->each(function (Frecuencia $frecuencia, int $index) use ($ciudades) {
            $ciudadParada = $ciudades[($index + 2) % $ciudades->count()];

            FrecuenciaParada::updateOrCreate(
                [
                    'frecuencia_id' => $frecuencia->id,
                    'orden' => 1,
                ],
                [
                    'ciudad_id' => $ciudadParada->id,
                    'minutos_desde_origen' => 90 + ($index * 20),
                ]
            );
        });

        /*
         |--------------------------------------------------------------------------
         | 5 rutas
         |--------------------------------------------------------------------------
         */
        $rutas = $frecuencias->map(function (Frecuencia $frecuencia, int $index) use ($buses) {
            return Ruta::updateOrCreate(
                [
                    'cooperativa_id' => $frecuencia->cooperativa_id,
                    'ciudad_origen_id' => $frecuencia->ciudad_origen_id,
                    'ciudad_destino_id' => $frecuencia->ciudad_destino_id,
                    'nombre' => 'Ruta Comercial '.($index + 1),
                ],
                [
                    'bus_id' => $buses[$index]->id,
                    'tipo_viaje' => 'con_paradas',
                    'distancia_km' => 120 + ($index * 35),
                    'duracion_minutos' => 180 + ($index * 25),
                    'activa' => true,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 salidas programadas
         |--------------------------------------------------------------------------
         */
        $salidas = $frecuencias->map(function (Frecuencia $frecuencia, int $index) use ($buses, $now) {
            return Salida::updateOrCreate(
                [
                    'bus_id' => $buses[$index]->id,
                    'fecha' => $now->copy()->addDays($index + 1)->toDateString(),
                    'hora_salida' => $frecuencia->hora_salida,
                ],
                [
                    'frecuencia_id' => $frecuencia->id,
                    'estado' => 'programada',
                    'precio_base' => 8 + ($index * 2),
                    'generada_automaticamente' => false,
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 boletos vendidos/pagados de prueba
         |--------------------------------------------------------------------------
         */
        $boletos = $salidas->map(function (Salida $salida, int $index) use ($admin, $buses) {
            $asiento = Asiento::where('bus_id', $buses[$index]->id)->orderBy('numero')->first();
            $frecuencia = Frecuencia::find($salida->frecuencia_id);
            $codigo = 'BOL-2026-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $precio = (float) $salida->precio_base + (float) ($asiento->tipoAsiento?->recargo ?? 0);

            return Boleto::firstOrCreate(
                [
                    'salida_id' => $salida->id,
                    'asiento_id' => $asiento->id,
                ],
                [
                    'user_id' => null,
                    'cliente_email' => 'cliente'.($index + 1).'@example.com',
                    'vendido_por' => $admin->id,
                    'ciudad_origen_id' => $frecuencia->ciudad_origen_id,
                    'ciudad_destino_id' => $frecuencia->ciudad_destino_id,
                    'codigo' => $codigo,
                    'pasajero_nombre' => 'Pasajero Demo '.($index + 1),
                    'pasajero_cedula' => '090000000'.($index + 1),
                    'tipo_descuento' => 'ninguno',
                    'porcentaje_descuento' => 0,
                    'precio' => $precio,
                    'estado' => 'pagado',
                    'vendido_at' => now(),
                ]
            );
        })->values();

        /*
         |--------------------------------------------------------------------------
         | 5 pagos de prueba
         |--------------------------------------------------------------------------
         */
        $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'deposito', 'efectivo'];

        $boletos->each(function (Boleto $boleto, int $index) use ($admin, $metodosPago) {
            Pago::updateOrCreate(
                ['boleto_id' => $boleto->id],
                [
                    'validado_por' => $admin->id,
                    'metodo' => $metodosPago[$index],
                    'monto' => $boleto->precio,
                    'comprobante_path' => null,
                    'estado' => 'validado',
                    'validado_at' => now(),
                    'observacion' => 'Pago de prueba validado para el boleto '.$boleto->codigo,
                ]
            );
        });

        /*
         |--------------------------------------------------------------------------
         | 5 registros de acceso
         |--------------------------------------------------------------------------
         */
        $boletos->each(function (Boleto $boleto, int $index) use ($admin) {
            RegistroAcceso::updateOrCreate(
                [
                    'boleto_id' => $boleto->id,
                    'resultado' => $index % 2 === 0 ? 'permitido' : 'rechazado',
                ],
                [
                    'registrado_por' => $admin->id,
                    'registrado_at' => now()->subMinutes(10 + $index),
                    'observacion' => $index % 2 === 0
                        ? 'Acceso permitido en prueba.'
                        : 'Acceso rechazado en prueba por validacion demo.',
                ]
            );
        });
    }
}
