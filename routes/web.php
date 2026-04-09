<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'inicio'])->name('inicio');
Route::get('/login', [WebController::class, 'login'])->name('login');
Route::get('/carga', [WebController::class, 'carga'])->name('carga');
Route::get('/resultado', [WebController::class, 'resultado'])->name('resultado');
