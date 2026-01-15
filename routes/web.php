<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\AdminUserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BirdsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineLogController;
use App\Http\Controllers\MortalitiesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\VaccinationLogController;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserController::class, 'login']);
    Route::get('register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [UserController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (all logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [UserController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/export', [DashboardController::class, 'exportPDF'])
        ->name('dashboard.export')
        ->middleware('role:admin');

    // Alerts & Notifications
    Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('notifications', [AlertController::class, 'view'])->name('notifications.index');
    Route::post('alerts/{alert}/read', [AlertController::class, 'read'])->name('alerts.read');
    Route::post('alerts/dismiss-all', [AlertController::class, 'dismissAll'])->name('alerts.dismiss-all');
    Route::post('alerts/custom/create', [AlertController::class, 'createCustom'])
        ->name('alerts.custom.create')
        ->middleware('role:admin');

    // Core resource routes
    Route::resources([
        'birds'            => BirdsController::class,
        'customers'        => CustomerController::class,
        'eggs'             => EggController::class,
        'employees'        => EmployeeController::class,
        'expenses'         => ExpenseController::class,
        'income'           => IncomeController::class,
        'inventory'        => InventoryController::class,
        'medicine-logs'    => MedicineLogController::class,
        'mortalities'      => MortalitiesController::class,
        'orders'           => OrderController::class,
        'payroll'          => PayrollController::class,
        'sales'            => SalesController::class,
        'suppliers'        => SupplierController::class,
        'vaccination-logs' => VaccinationLogController::class,
    ]);

    // Health checks (limited)
    Route::resource('health-checks', HealthCheckController::class)
        ->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::put('health-checks/{id}', [HealthCheckController::class, 'update'])->name('health-checks.update');

    // Eggs bulk delete
    Route::delete('eggs/bulk', [EggController::class, 'bulkDelete'])->name('eggs.bulkDelete');

    // Payroll extra actions
    Route::post('payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('payroll/export', [PayrollController::class, 'exportPDF'])
        ->name('payroll.export')
        ->middleware('role:admin');

    // Reports (✅ fixed, no duplicates)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/data', [ReportController::class, 'data'])->name('data');
    });

    // Diseases
    Route::prefix('diseases')->name('diseases.')->group(function () {
        Route::get('/', [DiseaseController::class, 'index'])->name('index');
        Route::get('/create', [DiseaseController::class, 'create'])->name('create');
        Route::post('/', [DiseaseController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DiseaseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DiseaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [DiseaseController::class, 'destroy'])->name('destroy');
        Route::get('/history', [DiseaseController::class, 'history'])->name('history');
    });

    // User role assignment (admin only)
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])
        ->name('users.assign-role')
        ->middleware('role:admin');

    Route::get('users/{user}/permissions', [AdminUserController::class, 'permissions'])->name('users.permissions');
    Route::post('users/{user}/permissions', [AdminUserController::class, 'update']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Settings & Search
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Inventory & Transactions
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionsController::class, 'index'])->name('index');
        Route::get('/{transaction}', [TransactionsController::class, 'show'])->name('show');
        Route::post('/{transaction}/approve', [TransactionsController::class, 'approve'])->name('approve');
        Route::post('/{transaction}/reject', [TransactionsController::class, 'reject'])->name('reject');
        Route::delete('/{transaction}', [TransactionsController::class, 'destroy'])->name('destroy');
    });

    // Activity Logs (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');
    });

    // Birds trash/restore
    Route::get('birds/trashed', [BirdsController::class, 'trashed'])->name('birds.trashed');
    Route::post('birds/{id}/restore', [BirdsController::class, 'restore'])->name('birds.restore');

    // Weather
    Route::get('/weather/fetch', [WeatherController::class, 'fetch'])->name('weather.fetch');

    // Feed consumption
    Route::prefix('feed')->name('feed.')->group(function () {
        Route::get('/consumption', [FeedController::class, 'consumption'])->name('consumption');
        Route::get('/consumption/create', [FeedController::class, 'consumptionCreate'])->name('consumption.create');
        Route::post('/consumption', [FeedController::class, 'storeConsumption'])->name('storeConsumption');
    });
    Route::resource('feed', FeedController::class)->except(['show']);
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', AdminUserController::class)->except('show');
    Route::post('users/{user}/toggle-permission', [AdminUserController::class, 'togglePermission'])
        ->name('users.toggle-permission');

    Route::resource('roles', RoleController::class)->only(['index', 'store']);
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

/*
|--------------------------------------------------------------------------
| Sales Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::get('/create', [SalesController::class, 'create'])->name('create');
    Route::post('/', [SalesController::class, 'store'])->name('store');
    Route::get('/{sale}/edit', [SalesController::class, 'edit'])->name('edit');
    Route::put('/{sale}', [SalesController::class, 'update'])->name('update');
    Route::delete('/{sale}', [SalesController::class, 'destroy'])->name('destroy');

    Route::get('/{sale}/invoice', [SalesController::class, 'invoice'])->name('invoice');
    Route::get('/{sale}/invoice/preview', [SalesController::class, 'invoicePreview'])->name('invoice.preview');
    Route::get('/pending-json', [SalesController::class, 'pendingJson'])->name('pendingJson');
    Route::post('/{sale}/record-payment', [SalesController::class, 'recordPayment'])->name('recordPayment');
    Route::post('/sales/{sale}/record-payment', [SalesController::class, 'recordPayment'])
    ->name('sales.recordPayment');

   
});

/*
|--------------------------------------------------------------------------
| Vaccination Logs Routes
|--------------------------------------------------------------------------
*/
Route::prefix('vaccination-logs')->middleware(['auth'])->name('vaccination-logs.')->group(function () {
    Route::get('/', [VaccinationLogController::class, 'index'])->name('index');
    Route::get('/create', [VaccinationLogController::class, 'create'])->name('create');
    Route::post('/', [VaccinationLogController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [VaccinationLogController::class, 'edit'])->name('edit');
    Route::put('/{id}', [VaccinationLogController::class, 'update'])->name('update');
    Route::delete('/{id}', [VaccinationLogController::class, 'destroy'])->name('destroy');
    Route::get('/reminders', [VaccinationLogController::class, 'reminders'])->name('reminders');
});
