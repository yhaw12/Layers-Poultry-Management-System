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
    // Authentication
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'exportPDF'])->name('dashboard.export');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Alerts (Admin Only)
    Route::post('alerts/{alert}/read', function (App\Models\Alert $alert) {
        $alert->update(['read_at' => now()]);
        return redirect()->back()->with('success', 'Alert marked as read.');
    })->name('alerts.read')->middleware('is_admin');

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
    Route::get('/eggs/sales', [SalesController::class, 'sales'])->name('eggs.sales');
    Route::get('/sales/birds', [SalesController::class, 'birdSales'])->name('sales.birds');
    Route::post('/payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('/payroll/export', [PayrollController::class, 'exportPDF'])->name('payroll.export');

    // Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
    Route::match(['get', 'post'], '/{type?}', [ReportController::class, 'index'])->name('index');
    Route::get('/custom', [ReportController::class, 'generateCustom'])->name('custom');
    Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
});