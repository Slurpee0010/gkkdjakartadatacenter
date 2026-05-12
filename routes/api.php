<?php

use App\Http\Controllers\Api\Admin\NotificationController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\UserDeletionRequestController;
use App\Http\Controllers\Api\Public\PublicLaporanBlesscomnController;
use App\Http\Controllers\Api\Public\PublicLaporanPaController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')
    ->middleware('throttle:public-submissions')
    ->group(function () {
        Route::post('/laporan-pa', [PublicLaporanPaController::class, 'store'])
            ->name('api.public.laporan-pa.store');
        Route::post('/blesscomn', [PublicLaporanBlesscomnController::class, 'store'])
            ->name('api.public.blesscomn.store');
    });

Route::prefix('admin')
    ->middleware(['auth', 'active.user'])
    ->group(function () {
        Route::get('/me', function () {
            return response()->json([
                'data' => request()->user()->load(['role.permissions', 'wilayah']),
            ]);
        })->name('api.admin.me');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users,read')
            ->name('api.admin.users.index');
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users,create')
            ->name('api.admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users,update')
            ->name('api.admin.users.update');
        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users,update')
            ->name('api.admin.users.patch');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users,delete')
            ->name('api.admin.users.destroy');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles,read')
            ->name('api.admin.roles.index');
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles,create')
            ->name('api.admin.roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles,update')
            ->name('api.admin.roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles,delete')
            ->name('api.admin.roles.destroy');

        Route::get('/user-deletion-requests', [UserDeletionRequestController::class, 'index'])
            ->middleware('permission:user_deletion_requests,read')
            ->name('api.admin.user-deletion-requests.index');
        Route::post('/user-deletion-requests/{userDeletionRequest}/approve', [UserDeletionRequestController::class, 'approve'])
            ->middleware('permission:user_deletion_requests,approve')
            ->name('api.admin.user-deletion-requests.approve');
        Route::post('/user-deletion-requests/{userDeletionRequest}/reject', [UserDeletionRequestController::class, 'reject'])
            ->middleware('permission:user_deletion_requests,reject')
            ->name('api.admin.user-deletion-requests.reject');

        Route::post('/notifications', [NotificationController::class, 'store'])
            ->middleware('permission:notifications,send')
            ->name('api.admin.notifications.store');
    });
