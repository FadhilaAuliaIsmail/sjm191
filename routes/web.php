<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\BarangProduksiController;
use App\Http\Controllers\DataMitraController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::put('/profile/security', [ProfileController::class, 'updateSecurity'])->name('profile.security.update');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== KHUSUS KASIR + PEMILIK =====
    Route::middleware('role:kasir,pemilik_usaha')->group(function () {
        Route::get('/kasir', [TransaksiController::class, 'pos'])->name('pos.index');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('transaksi.struk');
        Route::resource('produk', ProdukController::class)->except(['show']);
    });


    // ===== KHUSUS PEMILIK USAHA =====
    Route::middleware('role:pemilik_usaha')->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show', 'create', 'edit']);
        Route::resource('barang-produksi', BarangProduksiController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
        Route::resource('data-mitra', DataMitraController::class)->except(['show', 'create', 'edit']);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    });
});

require __DIR__ . '/auth.php';
