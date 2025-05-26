<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\BirdsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ChicksController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineLogController;
use App\Http\Controllers\MortalitiesController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'exportPDF'])->name('dashboard.export');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Resource Routes
    Route::resources([
        'expenses'        => ExpenseController::class,
        'chicks'          => ChicksController::class,
        'birds'           => BirdsController::class,
        'eggs'            => EggController::class,
        'income'          => IncomeController::class,
        'customers'       => CustomerController::class,
        'employees'       => EmployeeController::class,
        'medicine-logs'   => MedicineLogController::class,
        'payroll'         => PayrollController::class,
        'sales'           => SalesController::class,
        'inventory'       => InventoryController::class,
        'mortalities'     => MortalitiesController::class,
    ]);

    // Custom Functional Routes
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::get('/feed/consumption', [FeedController::class, 'consumption'])->name('feed.consumption');
    Route::get('/medicine-logs/buy', [MedicineLogController::class, 'buy'])->name('medicine-logs.buy');
    Route::get('/medicine-logs/use', [MedicineLogController::class, 'use'])->name('medicine-logs.use');

    // Sales Subroutes
    Route::get('/eggs/sales', [SalesController::class, 'sales'])->name('eggs.sales');
    Route::get('/sales/birds', [SalesController::class, 'birdSales'])->name('sales.birds');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/weekly', [ReportController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
    });

    // Payroll
    Route::post('/payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('/payroll/export', [PayrollController::class, 'exportPDF'])->name('payroll.export');
});
