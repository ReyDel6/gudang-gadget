<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendReset'])->name('password.email');
    Route::get('/reset-password', [LoginController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'storeReset'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [GadgetController::class, 'landing'])->name('landing');
    Route::get('/gadget/create', [GadgetController::class, 'create'])->name('gadget.create');
    Route::post('/gadget', [GadgetController::class, 'store'])->name('gadget.store');
    Route::get('/gadget',  [GadgetController::class, 'index'])->name('gadget.index');
    Route::get('/gadget/export', [GadgetController::class, 'export'])->name('gadget.export');
    Route::get('/gadget/{id}', [GadgetController::class, 'show'])->name('gadget.show');
    Route::post('/gadget/{id}/stok/{arah}', [GadgetController::class, 'ubahStok'])->name('gadget.stok');
    Route::get('/gadget/{id}/edit', [GadgetController::class, 'edit'])->name('gadget.edit');
    Route::put('/gadget/{id}', [GadgetController::class, 'update'])->name('gadget.update');
    Route::delete('/gadget/{id}', [GadgetController::class, 'destroy'])->name('gadget.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});