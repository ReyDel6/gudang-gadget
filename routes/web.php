<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\LoginController;

Route::get('/register', [LoginController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [LoginController::class, 'register'])->middleware('guest');
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [GadgetController::class, 'landing'])->name('landing');
    Route::get('/gadget/create', [GadgetController::class, 'create'])->name('gadget.create');
    Route::post('/gadget', [GadgetController::class, 'store'])->name('gadget.store');
    Route::get('/gadget',  [GadgetController::class, 'index'])->name('gadget.index');
    Route::get('/gadget/{id}', [GadgetController::class, 'show'])->name('gadget.show');
    Route::post('/gadget/{id}/stok/{arah}', [GadgetController::class, 'ubahStok'])->name('gadget.stok');
    Route::get('/gadget/{id}/edit', [GadgetController::class, 'edit'])->name('gadget.edit');
    Route::put('/gadget/{id}', [GadgetController::class, 'update'])->name('gadget.update');
    Route::delete('/gadget/{id}', [GadgetController::class, 'destroy'])->name('gadget.destroy');
});
