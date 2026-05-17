<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('inicio');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () { return '<h1>Dashboard en construcción</h1>'; })->name('dashboard');
