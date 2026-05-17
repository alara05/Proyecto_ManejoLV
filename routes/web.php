<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('inicio');

Route::get('/login', [LoginController::class, 'show'])->name('login');

Route::post('/api/web/login', [LoginController::class, 'web'])->name('api.web.login');
Route::post('/api/mobile/login', [LoginController::class, 'mobile'])->name('api.mobile.login');
Route::post('/api/mobile/register', [RegisterController::class, 'mobile'])->name('api.mobile.register');
Route::post('/api/logout', [LogoutController::class, 'logout'])->name('api.logout');

Route::options('/api/{any}', function (Request $request) {
    return response('', 204)->withHeaders([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept',
    ]);
})->where('any', '.*');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/cooperativa/dashboard', [DashboardController::class, 'cooperativa'])->name('cooperativa.dashboard');
Route::get('/oficinista/dashboard', [DashboardController::class, 'oficinista'])->name('oficinista.dashboard');
Route::get('/pasajero/dashboard', [DashboardController::class, 'pasajero'])->name('pasajero.dashboard');
Route::get('/personal-bus/dashboard', [DashboardController::class, 'personalBus'])->name('personal_bus.dashboard');

if (class_exists(\App\Http\Controllers\Cooperativa\CooperativaController::class)) {
    Route::resource('/cooperativa/cooperativas', \App\Http\Controllers\Cooperativa\CooperativaController::class)
        ->except(['show']);
}

if (class_exists(\App\Http\Controllers\Cooperativa\BusController::class)) {
    Route::resource('/cooperativa/buses', \App\Http\Controllers\Cooperativa\BusController::class)
        ->except(['show']);
}

if (class_exists(\App\Http\Controllers\Cooperativa\RutaController::class)) {
    Route::resource('/cooperativa/rutas', \App\Http\Controllers\Cooperativa\RutaController::class)
        ->except(['show']);
}
