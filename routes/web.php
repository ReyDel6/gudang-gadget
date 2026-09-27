<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MitraAdminController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StoreAdminController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest', 'throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Storefront publik (tanpa login).
Route::get('/shop', [StorefrontController::class, 'index'])->name('shop.home');
Route::get('/shop/katalog', [StorefrontController::class, 'katalog'])->name('shop.katalog');
Route::get('/shop/produk/{id}', [StorefrontController::class, 'produk'])->name('shop.produk');

// Portal mitra reseller (B2B).
Route::middleware('guest')->group(function () {
    Route::get('/shop/mitra/daftar', [MitraController::class, 'daftar'])->name('shop.mitra.daftar');
    Route::post('/shop/mitra/daftar', [MitraController::class, 'daftarStore'])->name('shop.mitra.daftar.store')->middleware('throttle:5,10');
    Route::get('/shop/mitra/masuk', [MitraController::class, 'masuk'])->name('shop.mitra.masuk');
    Route::post('/shop/mitra/masuk', [MitraController::class, 'masukStore'])->name('shop.mitra.masuk.store')->middleware('throttle:5,10');
    Route::post('/shop/mitra/demo', [MitraController::class, 'demoMasuk'])->name('shop.mitra.demo.masuk')->middleware('throttle:5,10');
});
Route::post('/shop/mitra/keluar', [MitraController::class, 'keluar'])->name('shop.mitra.keluar')->middleware('auth');

Route::middleware(['auth', 'role:reseller'])->group(function () {
    Route::get('/shop/mitra/beranda', [MitraController::class, 'beranda'])->name('shop.mitra.beranda');
    Route::get('/shop/mitra/price-list', [MitraController::class, 'priceList'])->name('shop.mitra.price-list');
    Route::get('/shop/mitra/price-list/csv', [MitraController::class, 'priceListCsv'])->name('shop.mitra.price-list.csv');
    Route::get('/shop/mitra/price-list/cetak', [MitraController::class, 'priceListPrint'])->name('shop.mitra.price-list.cetak');
});

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendReset'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/reset-password', [LoginController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'storeReset'])->name('password.store')->middleware('throttle:5,1');
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/', [GadgetController::class, 'landing'])->name('landing');

    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::post('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    Route::get('/laporan/export-xls', [LaporanController::class, 'exportXls'])->name('laporan.export-xls');
    Route::get('/laporan/cetak', [LaporanController::class, 'print'])->name('laporan.print');
    Route::get('/laporan/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
    Route::get('/laporan/laba-rugi/cetak', [LaporanController::class, 'labaRugiPrint'])->name('laporan.laba-rugi.cetak');
    Route::get('/laporan/per-bulan/{tahun}', [LaporanController::class, 'perBulan'])->name('laporan.per-bulan');
    Route::get('/laporan/penjualan-harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/penjualan-harian/export', [LaporanController::class, 'harianCsv'])->name('laporan.harian-export');

    // Modul Kasir / POS.
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

    // Shift kasir.
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift/buka', [ShiftController::class, 'buka'])->name('shift.buka');
    Route::post('/shift/{id}/tutup', [ShiftController::class, 'tutup'])->name('shift.tutup');

    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/create', [MutasiController::class, 'create'])->name('mutasi.create');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');

    // Transaksi penjualan & pembelian.
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::get('/penjualan/{id}/cetak', [PenjualanController::class, 'cetak'])->name('penjualan.cetak');
    Route::get('/penjualan/{id}/label', [PenjualanController::class, 'label'])->name('penjualan.label');
    Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy')->middleware('role:admin');

    Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/create', [PembelianController::class, 'create'])->name('pembelian.create');
    Route::post('/pembelian', [PembelianController::class, 'store'])->name('pembelian.store');
    Route::get('/pembelian/{id}', [PembelianController::class, 'show'])->name('pembelian.show');
    Route::get('/pembelian/{id}/cetak', [PembelianController::class, 'cetak'])->name('pembelian.cetak');
    Route::delete('/pembelian/{id}', [PembelianController::class, 'destroy'])->name('pembelian.destroy')->middleware('role:admin');

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
    Route::get('/gadget/{id}/kartu-stok', [GadgetController::class, 'kartuStok'])->name('gadget.kartu-stok');
    Route::get('/gadget/{id}/barcode', [GadgetController::class, 'barcode'])->name('gadget.barcode');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('user.index');
        Route::post('/users', [UserController::class, 'store'])->name('user.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('/master/kategori', [MasterController::class, 'kategori'])->name('master.kategori');
        Route::post('/master/kategori', [MasterController::class, 'storeKategori'])->name('master.kategori.store');
        Route::delete('/master/kategori/{id}', [MasterController::class, 'destroyKategori'])->name('master.kategori.destroy');
        Route::get('/master/supplier', [MasterController::class, 'supplier'])->name('master.supplier');
        Route::post('/master/supplier', [MasterController::class, 'storeSupplier'])->name('master.supplier.store');
        Route::delete('/master/supplier/{id}', [MasterController::class, 'destroySupplier'])->name('master.supplier.destroy');

        Route::get('/mitra', [MitraAdminController::class, 'index'])->name('mitra.index');
        Route::post('/mitra/{id}/status', [MitraAdminController::class, 'setStatus'])->name('mitra.status');

Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    });

    // Storefront admin.
    Route::get('/store/banner', [StoreAdminController::class, 'banners'])->name('store.banner.index');
    Route::post('/store/banner', [StoreAdminController::class, 'bannerStore'])->name('store.banner.store');
    Route::put('/store/banner/{id}', [StoreAdminController::class, 'bannerUpdate'])->name('store.banner.update');
    Route::delete('/store/banner/{id}', [StoreAdminController::class, 'bannerDestroy'])->name('store.banner.destroy');
    Route::get('/store/settings', [StoreAdminController::class, 'settings'])->name('store.settings');
    Route::post('/store/settings', [StoreAdminController::class, 'settingsStore'])->name('store.settings.store');
    Route::post('/store/barang/{id}/toggle', [StoreAdminController::class, 'toggleGadget'])->name('store.barang.toggle');
});