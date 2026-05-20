<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AsientoController;
use App\Http\Controllers\AccesoPasajeroController;
use App\Http\Controllers\BusquedaViajeController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\BoletoPdfController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\ClienteBoletoController;
use App\Http\Controllers\ClienteHistorialBoletoController;
use App\Http\Controllers\ConfiguracionAplicacionController;
use App\Http\Controllers\CooperativaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\TipoAsientoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('inicio');
Route::get('/buscar-viajes', BusquedaViajeController::class)->name('viajes.buscar');
Route::get('/comprar-boleto', [ClienteBoletoController::class, 'create'])->name('cliente.boletos.create');
Route::post('/comprar-boleto', [ClienteBoletoController::class, 'store'])->name('cliente.boletos.store');
Route::get('/comprar-boleto/{boleto}/confirmacion', [ClienteBoletoController::class, 'show'])->name('cliente.boletos.show');
Route::get('/boletos/{boleto}/pdf', BoletoPdfController::class)->name('cliente.boletos.pdf');
Route::get('/boletos/{boleto}/pago', [PagoController::class, 'create'])->name('cliente.pagos.create');
Route::post('/boletos/{boleto}/pago', [PagoController::class, 'store'])->name('cliente.pagos.store');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/historial-compras', ClienteHistorialBoletoController::class)->name('cliente.boletos.historial');
    Route::get('/historial-boletos', ClienteHistorialBoletoController::class)->name('cliente.boletos.historial.legacy');
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/pdf', [NotificacionController::class, 'exportarPdf'])->name('notificaciones.pdf');
    Route::patch('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
    Route::resource('cooperativas', CooperativaController::class);
    Route::resource('provincias', ProvinciaController::class);
    Route::resource('ciudades', CiudadController::class)->parameters(['ciudades' => 'ciudad']);
    Route::resource('buses', BusController::class);
    Route::resource('tipo-asientos', TipoAsientoController::class);
    Route::resource('asientos', AsientoController::class);
    Route::resource('rutas', RutaController::class);
    Route::resource('salidas', SalidaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('boletos', BoletoController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
    Route::patch('pagos/{pago}/validar', [PagoController::class, 'validar'])->name('pagos.validar');
    Route::patch('pagos/{pago}/rechazar', [PagoController::class, 'rechazar'])->name('pagos.rechazar');
    Route::get('accesos', [AccesoPasajeroController::class, 'index'])->name('accesos.index');
    Route::post('accesos', [AccesoPasajeroController::class, 'store'])->name('accesos.store');
    Route::get('configuracion', [ConfiguracionAplicacionController::class, 'edit'])->name('configuracion.edit');
    Route::put('configuracion', [ConfiguracionAplicacionController::class, 'update'])->name('configuracion.update');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
