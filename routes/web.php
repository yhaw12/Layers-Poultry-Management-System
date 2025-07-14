<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BirdsController;
use App\Http\Controllers\ChicksController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineLogController;
use App\Http\Controllers\MortalitiesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VaccinationLogController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'exportPDF'])->name('dashboard.export');

    // Activity Logs (Admin Only)
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('is_admin');

    // Alerts (Admin Only)
    Route::post('alerts/{alert}/read', [ActivityLogController::class, 'read'])->name('alerts.read')->middleware('is_admin');
    Route::post('/alerts/dismiss-all', [ActivityLogController::class, 'dismissAll'])->name('alerts.dismiss-all')->middleware('is_admin');

    // Resource Routes
    Route::resources([
        'birds' => BirdsController::class,
        'chicks' => ChicksController::class,
        'customers' => CustomerController::class,
        'eggs' => EggController::class,
        'employees' => EmployeeController::class,
        'expenses' => ExpenseController::class,
        'feed' => FeedController::class,
        'income' => IncomeController::class,
        'inventory' => InventoryController::class,
        'medicine-logs' => MedicineLogController::class,
        'mortalities' => MortalitiesController::class,
        'orders' => OrderController::class,
        'payroll' => PayrollController::class,
        'sales' => SalesController::class,
        'suppliers' => SupplierController::class,
        'vaccination-logs' => VaccinationLogController::class,
    ]);

    // Custom Routes
    Route::delete('eggs/bulk', [EggController::class, 'bulkDelete'])->name('eggs.bulkDelete');
    Route::get('/feed/consumption', [FeedController::class, 'consumption'])->name('feed.consumption');
    Route::get('/medicine-logs/purchase', [MedicineLogController::class, 'index'])->name('medicine-logs.purchase');
    Route::get('/medicine-logs/consumption', [MedicineLogController::class, 'index'])->name('medicine-logs.consumption');
    Route::get('/sales/eggs', [SalesController::class, 'sales'])->name('eggs.sales');
    Route::get('/sales/birds', [SalesController::class, 'birdSales'])->name('sales.birds');
    Route::post('/payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('/payroll/export', [PayrollController::class, 'exportPDF'])->name('payroll.export');
    Route::get('/sales/{sale}/invoice', [SalesController::class, 'invoice'])->name('sales.invoice');
    Route::get('/invoices', [SalesController::class, 'invoices'])->name('invoices.index');
    Route::post('/sales/{sale}/status', [SalesController::class, 'updateStatus'])->name('sales.updateStatus');

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::match(['get', 'post'], '/{type?}', [ReportController::class, 'index'])->name('index');
        Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
    });

    // Permission-based Sales Routes
    Route::middleware(['permission:view-sales'])->group(function () {
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    });
    Route::middleware(['permission:create-sales'])->group(function () {
        Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    });
});