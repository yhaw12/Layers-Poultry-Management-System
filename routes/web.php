<?php

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
use Illuminate\Support\Facades\Route;

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

    // Alerts
    Route::get('alerts', [AlertController::class, 'view'])->name('alerts.index');
    Route::post('alerts/{id}/read', [AlertController::class, 'markAsRead'])->name('alerts.read');
    Route::post('alerts/dismiss-all', [AlertController::class, 'dismissAll'])->name('alerts.dismiss-all');
    Route::post('alerts/custom/create', [AlertController::class, 'createCustom'])->name('alerts.custom.create')
         ->middleware('role:admin');

    // Core resources
    Route::resources([
        'birds'            => BirdsController::class,
        'customers'        => CustomerController::class,
        'eggs'             => EggController::class,
        'employees'        => EmployeeController::class,
        'expenses'         => ExpenseController::class,
        'feed'             => FeedController::class,
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

    // Health Checks
    Route::resource('health-checks', HealthCheckController::class)->only(['index', 'create', 'store']);

    // Other routes
    Route::delete('eggs/bulk', [EggController::class, 'bulkDelete'])->name('eggs.bulkDelete');
    Route::get('feed/consumption', [FeedController::class, 'consumption'])->name('feed.consumption');
    Route::get('medicine-logs/purchase', [MedicineLogController::class, 'purchase'])->name('medicine-logs.purchase');
    Route::get('medicine-logs/consumption', [MedicineLogController::class, 'consumption'])->name('medicine-logs.consumption');
    Route::get('sales/eggs', [SalesController::class, 'sales'])->name('eggs.sales');
    Route::get('sales/birds', [SalesController::class, 'birdSales'])->name('sales.birds');
    Route::post('sales/{sale}/status', [SalesController::class, 'updateStatus'])->name('sales.updateStatus');
    Route::get('sales/{sale}/invoice', [SalesController::class, 'invoice'])->name('sales.invoice');
    Route::get('sales/{sale}/email', [SalesController::class, 'emailInvoice'])->name('sales.emailInvoice');
    Route::post('sales/{sale}/payment', [SalesController::class, 'recordPayment'])->name('sales.recordPayment');
    Route::get('invoices', [SalesController::class, 'invoices'])->name('invoices.index');
    Route::post('payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('payroll/export', [PayrollController::class, 'exportPDF'])->name('payroll.export')->middleware('role:admin');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('export', [ReportController::class, 'export'])->name('export');
        Route::match(['get', 'post'], '{type?}', [ReportController::class, 'index'])->name('index');
        Route::get('custom', [ReportController::class, 'custom'])->name('custom');
    });
    Route::get('diseases', [DiseaseController::class, 'index'])->name('diseases.index');
    Route::get('diseases/{disease}/history', [DiseaseController::class, 'history'])->name('diseases.history');
    Route::post('diseases', [DiseaseController::class, 'store'])->name('diseases.store');
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role')->middleware('role:admin');
    Route::post('/users/{user}/toggle-permission', [AdminUserController::class, 'togglePermission'])->name('users.toggle-permission');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/alerts/low-stock', [InventoryController::class, 'lowStock'])->name('alerts.low-stock');
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transaction}/approve', [TransactionsController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionsController::class, 'reject'])->name('transactions.reject');

    // Soft delete routes
    Route::get('birds/trashed', [BirdsController::class, 'trashed'])->name('birds.trashed');
    Route::post('birds/{id}/restore', [BirdsController::class, 'restore'])->name('birds.restore');

    
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', AdminUserController::class)->except('show');
    Route::resource('roles', RoleController::class)->only(['index', 'store']);
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});