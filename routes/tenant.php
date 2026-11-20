<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Web Controllers
use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\SalonController;
use App\Http\Controllers\Tenant\TaskCategoryController;
use App\Http\Controllers\Tenant\SurveyQuestionController;
use App\Http\Controllers\Tenant\DesignationController;
use App\Http\Controllers\Tenant\EmployeeController;
use App\Http\Controllers\Tenant\SystemUserController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\ReportController;

// API V1 Controllers
use App\Http\Controllers\Api\V1\AuthController as ApiAuthController;
use App\Http\Controllers\Api\V1\StoreController as ApiStoreController;
use App\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\V1\TaskController as ApiTaskController;
use App\Http\Controllers\Api\V1\NotificationController as ApiNotificationController;

/*
|--------------------------------------------------------------------------
| Tenant Web Routes (Browser Sessions)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Root Redirect
    Route::get('/', function () {
        if (auth()->guard('web')->check()) {
            return redirect()->route('tenant.dashboard');
        }
        return redirect()->route('tenant.login');
    });

    // Guest Auth Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/login', [LoginController::class, 'login'])->name('tenant.login.post');

    // Authenticated Tenant Portal
    Route::middleware('auth:web')->name('tenant.')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Store / Salon Management
        Route::resource('salons', SalonController::class);

        // Staff & Salon Employee Directory
        Route::resource('employees', EmployeeController::class);

        // Salon Designations / Job Titles
        Route::resource('designations', DesignationController::class);

        // Task Categories
        Route::resource('task-categories', TaskCategoryController::class);

        // Daily Survey Tasks & Questions
        Route::resource('tasks', SurveyQuestionController::class);

        // System Portal Users
        Route::resource('system-users', SystemUserController::class);

        // Security Roles & Module Permissions
        Route::resource('roles', RoleController::class);

        // Reports & Analytics Suite
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/task-compliance', [ReportController::class, 'taskCompliance'])->name('task-compliance');
            Route::get('/survey-responses', [ReportController::class, 'surveyResponses'])->name('survey-responses');
            Route::get('/export-compliance', [ReportController::class, 'exportCompliance'])->name('export-compliance');
        });
    });

    // Dynamic Location Helper (States by Country)
    Route::get('/countries/{country}/states', [SalonController::class, 'getStates'])->name('countries.states');
});

/*
|--------------------------------------------------------------------------
| Tenant RESTful Mobile & API Routes (Sanctum Token Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api/v1')->group(function () {

    // Public Authentication Endpoints
    Route::prefix('auth')->group(function () {
        Route::post('login', [ApiAuthController::class, 'login']);
        Route::post('generate_otp', [ApiAuthController::class, 'generateOtp']);
        Route::post('verify_otp', [ApiAuthController::class, 'verifyOtp']);
    });

    // Legacy Route Aliases for mobile backwards compatibility
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::post('generate_otp', [ApiAuthController::class, 'generateOtp']);
    Route::post('verify_otp', [ApiAuthController::class, 'verifyOtp']);

    // Protected API Endpoints (Requires Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [ApiAuthController::class, 'me']);
        Route::post('auth/logout', [ApiAuthController::class, 'logout']);
        Route::post('logout', [ApiAuthController::class, 'logout']);

        // Stores & Salons
        Route::get('stores', [ApiStoreController::class, 'index']);
        Route::post('stores_list', [ApiStoreController::class, 'index']); // Legacy alias
        Route::get('stores/{salon}', [ApiStoreController::class, 'show']);

        // Task Categories
        Route::get('categories', [ApiCategoryController::class, 'index']);

        // Daily / Weekly / Monthly Survey Tasks
        Route::get('tasks/daily', [ApiTaskController::class, 'dailyTasks']);
        Route::post('daily_task_list', [ApiTaskController::class, 'dailyTasks']); // Legacy alias
        Route::get('tasks/weekly', [ApiTaskController::class, 'weeklyTasks']);
        Route::post('weekly_task_list', [ApiTaskController::class, 'weeklyTasks']); // Legacy alias
        Route::get('tasks/monthly', [ApiTaskController::class, 'monthlyTasks']);
        Route::post('monthly_task_list', [ApiTaskController::class, 'monthlyTasks']); // Legacy alias

        // Answer Submissions
        Route::match(['POST', 'PATCH'], 'tasks/submit', [ApiTaskController::class, 'submitAnswers']);
        Route::match(['POST', 'PATCH'], 'submit_answers', [ApiTaskController::class, 'submitAnswers']); // Legacy alias

        // Submission History
        Route::get('tasks/history', [ApiTaskController::class, 'history']);

        // Push Notifications
        Route::post('notifications/subscribe', [ApiNotificationController::class, 'subscribe']);
        Route::post('notification_subscription_request', [ApiNotificationController::class, 'subscribe']); // Legacy alias
    });
});