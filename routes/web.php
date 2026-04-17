<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AdminController;

Route::get('/', [WebController::class, 'inicio'])->name('inicio');
Route::get('/login', [WebController::class, 'login'])->name('login');
Route::post('/login', [WebController::class, 'storeParticipante'])->name('login.store');
Route::get('/carga', [WebController::class, 'carga'])->name('carga');
Route::post('/carga', [WebController::class, 'storeImagen'])->name('carga.store');
Route::post('/analizar', [WebController::class, 'analizarImagen'])->name('analizar');
Route::get('/resultado', [WebController::class, 'resultado'])->name('resultado');
Route::post('/resultados', [WebController::class, 'storeResultados'])->name('resultados.store');
Route::get('/resultados', [WebController::class, 'resultados'])->name('resultados');
Route::post('/guardar', [WebController::class, 'guardar'])->name('guardar');
Route::get('/img/{path}', [WebController::class, 'serveImagen'])->where('path', '.+')->name('img');

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminController::class, 'loginForm'])->name('login');
    Route::post('login', [AdminController::class, 'loginPost'])->name('login.post');
    Route::post('logout', [AdminController::class, 'logout'])->name('logout');
    Route::get('register', [AdminController::class, 'registerForm'])->name('register');
    Route::post('register', [AdminController::class, 'registerPost'])->name('register.post');

    Route::middleware('auth')->group(function () {
        Route::get('participantes', [AdminController::class, 'participantes'])->name('participantes');
        Route::delete('participantes/{participante}', [AdminController::class, 'destroyParticipante'])->name('participantes.destroy');
    });
});
