<?php

use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\EncomiendaController;
use App\Http\Controllers\Api\Mobile\PaymentController;
use App\Http\Controllers\Api\Mobile\ProfileController;
use App\Http\Controllers\Api\Mobile\TicketController;
use App\Http\Controllers\Api\Mobile\TravelController;
use App\Http\Middleware\MobileTokenAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(MobileTokenAuth::class)->group(function (): void {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('travels', [TravelController::class, 'index']);
        Route::get('travels/{salida}', [TravelController::class, 'show']);

        Route::get('tickets', [TicketController::class, 'index']);
        Route::post('tickets', [TicketController::class, 'store']);
        Route::get('tickets/{boleto}', [TicketController::class, 'show']);

        Route::post('tickets/{boleto}/payments', [PaymentController::class, 'store']);
        Route::get('encomiendas', [EncomiendaController::class, 'index']);
    });
});
