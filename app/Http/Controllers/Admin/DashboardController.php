<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'tituloPanel' => 'Dashboard web',
            'subtituloPanel' => 'Panel principal del sistema de pasajes.',
            'estadisticas' => $this->estadisticasGenerales(),
        ]);
    }

    public function cooperativa(): View
    {
        return view('cooperativa.dashboard', [
            'tituloPanel' => 'Dashboard cooperativa',
            'subtituloPanel' => 'Gestión de buses, frecuencias y hojas de ruta.',
            'estadisticas' => $this->estadisticasGenerales(),
        ]);
    }

    public function oficinista(): View
    {
        return view('admin.dashboard', [
            'tituloPanel' => 'Dashboard oficinista',
            'subtituloPanel' => 'Panel para ventas y validación de pagos.',
            'estadisticas' => $this->estadisticasGenerales(),
        ]);
    }

    public function pasajero(): View
    {
        return view('pasajero.dashboard', [
            'tituloPanel' => 'Dashboard pasajero',
            'subtituloPanel' => 'Consulta viajes, boletos e historial de compras.',
            'estadisticas' => $this->estadisticasGenerales(),
        ]);
    }

    public function personalBus(): View
    {
        return view('personal_bus.escanear', [
            'tituloPanel' => 'Dashboard personal del bus',
            'subtituloPanel' => 'Panel para validar el acceso de pasajeros.',
            'estadisticas' => $this->estadisticasGenerales(),
        ]);
    }

    private function estadisticasGenerales(): array
    {
        return [
            'usuarios' => $this->contar('USUARIO'),
            'cooperativas' => $this->contar('COOPERATIVA'),
            'buses' => $this->contar('BUS'),
            'rutas' => $this->contar('RUTA'),
            'boletos' => $this->contar('BOLETO'),
        ];
    }

    private function contar(string $tabla): int
    {
        if (! Schema::hasTable($tabla)) {
            return 0;
        }

        return (int) DB::table($tabla)->count();
    }
}
