<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LogController::class, 'index'])->name('logs.index');
Route::get('/logs/export', [LogController::class, 'export'])->name('logs.export');
Route::post('/logs', [LogController::class, 'store'])->name('logs.store');
Route::patch('/logs/{id}/checkout', [LogController::class, 'checkout'])->name('logs.checkout');
