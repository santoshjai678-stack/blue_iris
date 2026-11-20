<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\Auth\LoginController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\TenantController;
use App\Http\Controllers\Api\V1\TenantLookupController;

/*
|--------------------------------------------------------------------------
| Central Web Routes (Platform SuperAdmin & Central API)
|--------------------------------------------------------------------------
| Only accessible on central domains (e.g. localhost, 127.0.0.1)
*/

foreach (config('tenancy.central_domains', ['localhost', '127.0.0.1']) as $domain) {
    Route::domain($domain)->group(function () {
        // Root Redirect for Central
        Route::get('/', function () {
            if (auth()->guard('superadmin')->check()) {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect()->route('superadmin.login');
        });

        // Central Tenant Discovery / Lookup API (for Mobile Apps - Stateless API)
        Route::prefix('api/v1')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])->group(function () {
            Route::post('tenant-lookup', [TenantLookupController::class, 'lookup']);
            Route::get('tenant-lookup', [TenantLookupController::class, 'lookup']);
        });

        // SuperAdmin Guest Routes (Login)
        Route::prefix('superadmin')->name('superadmin.')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        });

        // SuperAdmin Protected Routes (Requires 'superadmin' guard session)
        Route::prefix('superadmin')
            ->name('superadmin.')
            ->middleware('auth:superadmin')
            ->group(function () {
                Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
                Route::resource('tenants', TenantController::class);
            });
    });
}