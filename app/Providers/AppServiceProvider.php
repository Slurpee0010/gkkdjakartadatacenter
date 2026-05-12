<?php

namespace App\Providers;

use App\Models\AnakBimbingan;
use App\Models\AppNotification;
use App\Models\KehadiranIbadah;
use App\Models\LaporanBlesscomn;
use App\Models\LaporanPa;
use App\Models\MasterBlesscomn;
use App\Models\MasterBukuPa;
use App\Models\Pelayanan;
use App\Models\Pembimbing;
use App\Models\PengurusBlesscomn;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDeletionRequest;
use App\Models\Wilayah;
use App\Observers\AuditLogObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([
            AnakBimbingan::class,
            AppNotification::class,
            KehadiranIbadah::class,
            LaporanBlesscomn::class,
            LaporanPa::class,
            MasterBlesscomn::class,
            MasterBukuPa::class,
            Pelayanan::class,
            Pembimbing::class,
            PengurusBlesscomn::class,
            Permission::class,
            Role::class,
            User::class,
            UserDeletionRequest::class,
            Wilayah::class,
        ] as $model) {
            $model::observe(AuditLogObserver::class);
        }

        RateLimiter::for('public-submissions', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->ip()),
                Limit::perHour(100)->by($request->ip()),
            ];
        });
    }
}
