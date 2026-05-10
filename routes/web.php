<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelayananController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\AnakBimbinganController;
use App\Http\Controllers\MasterBukuPaController;
use App\Http\Controllers\LaporanPaController;
use App\Http\Controllers\PengurusBlesscomnController;
use App\Http\Controllers\MasterBlesscomnController;
use App\Http\Controllers\LaporanBlesscomnController;
use App\Http\Controllers\DashboardBlesscomnController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('pelayanan', PelayananController::class);
Route::resource('wilayah', WilayahController::class);
Route::resource('pembimbing', PembimbingController::class);
Route::resource('anak_bimbingan', AnakBimbinganController::class);
Route::resource('master_buku_pa', MasterBukuPaController::class);
Route::resource('laporan_pa', LaporanPaController::class);

// API endpoints untuk cascading dropdowns (Laporan PA)
Route::get('/api/get-pembimbing', [LaporanPaController::class, 'getPembimbing'])->name('api.get-pembimbing');
Route::get('/api/get-anak-pa', [LaporanPaController::class, 'getAnakPa'])->name('api.get-anak-pa');

// Ticket 3: Report Keaktifan PA + Export CSV
Route::get('/laporan-pa/report', [LaporanPaController::class, 'report'])->name('laporan_pa.report');
Route::get('/laporan-pa/export-csv', [LaporanPaController::class, 'exportCsv'])->name('laporan_pa.export-csv');
Route::get('/laporan-pa/export-excel', [LaporanPaController::class, 'exportExcel'])->name('laporan_pa.export-excel');
Route::get('/laporan-pa/export', [LaporanPaController::class, 'exportIndex'])->name('laporan_pa.export');

// =========================================
// Modul Blesscomn
// =========================================
Route::resource('pengurus_blesscomn', PengurusBlesscomnController::class);
Route::resource('master_blesscomn', MasterBlesscomnController::class);
Route::resource('laporan_blesscomn', LaporanBlesscomnController::class);
Route::get('/pengurus-blesscomn/export', [PengurusBlesscomnController::class, 'export'])->name('pengurus_blesscomn.export');
Route::get('/master-blesscomn/export', [MasterBlesscomnController::class, 'export'])->name('master_blesscomn.export');
Route::get('/laporan-blesscomn/export', [LaporanBlesscomnController::class, 'export'])->name('laporan_blesscomn.export');

// API: Cascading dropdown Blesscomn by Wilayah & Pelayanan
Route::get('/api/get-blesscomn', [MasterBlesscomnController::class, 'getBlesscomnByFilter'])->name('api.get-blesscomn');

// Dashboard Blesscomn & Export
Route::get('/dashboard-blesscomn', [DashboardBlesscomnController::class, 'index'])->name('dashboard_blesscomn');
Route::get('/dashboard-blesscomn/export-csv', [DashboardBlesscomnController::class, 'exportCsv'])->name('dashboard_blesscomn.export');
