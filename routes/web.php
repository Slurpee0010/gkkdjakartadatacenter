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
use App\Http\Controllers\KehadiranIbadahController;
use App\Http\Controllers\DashboardKehadiranIbadahController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\NotificationInboxController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\UserManagementController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard-utama', [PublicDashboardController::class, 'index'])->name('public.dashboard');
Route::get('/dashboard-utama/laporan-pa', [PublicDashboardController::class, 'laporanPa'])->name('public.laporan-pa');
Route::post('/dashboard-utama/laporan-pa', [PublicDashboardController::class, 'storeLaporanPa'])
    ->middleware('throttle:public-submissions')
    ->name('public.laporan-pa.store');
Route::get('/dashboard-utama/laporan-blesscomn', [PublicDashboardController::class, 'laporanBlesscomn'])->name('public.laporan-blesscomn');
Route::post('/dashboard-utama/laporan-blesscomn', [PublicDashboardController::class, 'storeLaporanBlesscomn'])
    ->middleware('throttle:public-submissions')
    ->name('public.laporan-blesscomn.store');
Route::get('/dashboard-utama/options/pembimbing', [PublicDashboardController::class, 'getPembimbing'])
    ->middleware('throttle:public-submissions')
    ->name('public.options.pembimbing');
Route::get('/dashboard-utama/options/anak-pa', [PublicDashboardController::class, 'getAnakPa'])
    ->middleware('throttle:public-submissions')
    ->name('public.options.anak-pa');
Route::get('/dashboard-utama/options/blesscomn', [PublicDashboardController::class, 'getBlesscomn'])
    ->middleware('throttle:public-submissions')
    ->name('public.options.blesscomn');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('/impersonate/stop', [ImpersonationController::class, 'destroy'])->name('impersonate.stop');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('permission:audit_logs,read')
        ->name('audit_logs.index');

    Route::get('/inbox', [NotificationInboxController::class, 'index'])
        ->middleware('permission:notifications,read')
        ->name('notifications.index');
    Route::get('/inbox/broadcast', [NotificationInboxController::class, 'create'])
        ->middleware('permission:notifications,send')
        ->name('notifications.create');
    Route::post('/inbox/broadcast', [NotificationInboxController::class, 'store'])
        ->middleware('permission:notifications,send')
        ->name('notifications.store');

    Route::middleware('permission:users,auto')->group(function () {
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

    Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'store'])
        ->middleware('permission:users,read')
        ->name('users.impersonate');

    Route::get('/dashboard-pa', [DashboardController::class, 'index'])
        ->middleware('permission:pa,read')
        ->name('dashboard');

    Route::middleware('permission:master_buku_pa,auto')->group(function () {
        Route::post('/master_buku_pa/{masterBukuPa}/approve', [MasterBukuPaController::class, 'approve'])
            ->middleware('permission:master_buku_pa,approve')
            ->name('master_buku_pa.approve');
        Route::post('/master_buku_pa/{masterBukuPa}/reject', [MasterBukuPaController::class, 'reject'])
            ->middleware('permission:master_buku_pa,reject')
            ->name('master_buku_pa.reject');
        Route::resource('master_buku_pa', MasterBukuPaController::class);
    });

    Route::middleware('permission:master_data,auto')->group(function () {
        Route::resource('pelayanan', PelayananController::class);
        Route::resource('wilayah', WilayahController::class);
    });

    Route::middleware('permission:pa,auto')->group(function () {
        Route::resource('pembimbing', PembimbingController::class);
        Route::resource('anak_bimbingan', AnakBimbinganController::class);
        Route::resource('laporan_pa', LaporanPaController::class);

        // API endpoints untuk cascading dropdowns (Laporan PA)
        Route::get('/api/get-pembimbing', [LaporanPaController::class, 'getPembimbing'])->name('api.get-pembimbing');
        Route::get('/api/get-anak-pa', [LaporanPaController::class, 'getAnakPa'])->name('api.get-anak-pa');

        // Ticket 3: Report Keaktifan PA + Export CSV
        Route::get('/laporan-pa/report', [LaporanPaController::class, 'report'])->name('laporan_pa.report');
        Route::get('/laporan-pa/export-csv', [LaporanPaController::class, 'exportCsv'])->name('laporan_pa.export-csv');
        Route::get('/laporan-pa/export-excel', [LaporanPaController::class, 'exportExcel'])->name('laporan_pa.export-excel');
        Route::get('/laporan-pa/export', [LaporanPaController::class, 'exportIndex'])->name('laporan_pa.export');
    });

    Route::middleware('permission:blesscomn,auto')->group(function () {
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
    });

    Route::middleware('permission:kehadiran_ibadah,auto')->group(function () {
        Route::resource('kehadiran_ibadah', KehadiranIbadahController::class)->except(['show']);
        Route::get('/kehadiran-ibadah/export', [KehadiranIbadahController::class, 'export'])->name('kehadiran_ibadah.export');
        Route::get('/api/kehadiran-ibadah/nama-default', [KehadiranIbadahController::class, 'previewNamaIbadah'])->name('api.kehadiran-ibadah.nama-default');

        Route::get('/dashboard-kehadiran-ibadah', [DashboardKehadiranIbadahController::class, 'index'])->name('dashboard_kehadiran_ibadah');
        Route::get('/dashboard-kehadiran-ibadah/export', [DashboardKehadiranIbadahController::class, 'export'])->name('dashboard_kehadiran_ibadah.export');
        Route::get('/api/kehadiran-ibadah/summary-mingguan', [DashboardKehadiranIbadahController::class, 'summaryWeeklyApi'])->name('api.kehadiran-ibadah.summary-mingguan');
        Route::get('/api/kehadiran-ibadah/rata-rata', [DashboardKehadiranIbadahController::class, 'averageAttendanceApi'])->name('api.kehadiran-ibadah.rata-rata');
    });
});
