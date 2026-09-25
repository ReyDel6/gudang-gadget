<?php

use App\Http\Controllers\GadgetController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest', 'throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendReset'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password', [LoginController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'storeReset'])->name('password.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [GadgetController::class, 'landing'])->name('landing');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');

    // Urutan penting: rute statis (/arsip, /import, /export) harus
    // dideklarasikan sebelum rute dinamis /gadget/{id}.
    Route::get('/gadget', [GadgetController::class, 'index'])->name('gadget.index');
    Route::get('/gadget/create', [GadgetController::class, 'create'])->name('gadget.create');
    Route::post('/gadget', [GadgetController::class, 'store'])->name('gadget.store');
    Route::get('/gadget/arsip', [GadgetController::class, 'archive'])->name('gadget.archive')->middleware('role:admin');
    Route::post('/gadget/arsip/{id}/restore', [GadgetController::class, 'restore'])->name('gadget.restore')->middleware('role:admin');
    Route::delete('/gadget/arsip/{id}/force', [GadgetController::class, 'forceDestroy'])->name('gadget.force-destroy')->middleware('role:admin');
    Route::get('/gadget/import', [GadgetController::class, 'importForm'])->name('gadget.import');
    Route::post('/gadget/import', [GadgetController::class, 'importStore'])->name('gadget.import-store')->middleware('role:admin');
    Route::get('/gadget/import/template', [GadgetController::class, 'importTemplate'])->name('gadget.import-template');
    Route::get('/gadget/export', [GadgetController::class, 'export'])->name('gadget.export');
    Route::get('/gadget/{id}', [GadgetController::class, 'show'])->name('gadget.show');
    Route::post('/gadget/{id}/stok/{arah}', [GadgetController::class, 'ubahStok'])->name('gadget.stok');
    Route::post('/gadget/{id}/transfer', [GadgetController::class, 'transfer'])->name('gadget.transfer')->middleware('role:admin');
    Route::get('/gadget/{id}/edit', [GadgetController::class, 'edit'])->name('gadget.edit');
    Route::put('/gadget/{id}', [GadgetController::class, 'update'])->name('gadget.update');
    Route::delete('/gadget/{id}', [GadgetController::class, 'destroy'])->name('gadget.destroy')->middleware('role:admin');
    Route::get('/gadget/{id}/barcode', [GadgetController::class, 'barcode'])->name('gadget.barcode');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::post('/users', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });
});