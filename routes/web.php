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
use App\Http\Controllers\WeatherController;
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

    // Alerts/Notifications
    Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('notifications', [AlertController::class, 'view'])->name('notifications.index');
    Route::post('alerts/{alert}/read', [AlertController::class, 'read'])->name('alerts.read');
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
    Route::resource('health-checks', HealthCheckController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);

    // Other routes
    Route::delete('eggs/bulk', [EggController::class, 'bulkDelete'])->name('eggs.bulkDelete');
    Route::get('feed/consumption', [FeedController::class, 'consumption'])->name('feed.consumption');
    Route::get('sales/eggs', [SalesController::class, 'sales'])->name('eggs.sales');
    Route::get('sales/birds', [SalesController::class, 'birdSales'])->name('sales.birds');
    // Route::post('sales/{sale}/status', [SalesController::class, 'updateStatus'])->name('sales.updateStatus');
    // Route::get('sales/{sale}/invoice', [SalesController::class, 'invoice'])->name('sales.invoice');
    // Route::get('sales/{sale}/email', [SalesController::class, 'emailInvoice'])->name('sales.emailInvoice');
    // Route::post('sales/{sale}/payment', [SalesController::class, 'recordPayment'])->name('sales.recordPayment');
    // Route::get('sales/pending-payments', [SalesController::class, 'pendingPayments'])->name('sales.pendingPayments');
    Route::post('payroll/generate', [PayrollController::class, 'generateMonthly'])->name('payroll.generate');
    Route::get('payroll/export', [PayrollController::class, 'exportPDF'])->name('payroll.export')->middleware('role:admin');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/data', [ReportController::class, 'data'])->name('reports.data');
    Route::get('diseases', [DiseaseController::class, 'index'])->name('diseases.index');
     Route::get('/reports/custom', [ReportController::class, 'custom'])->name('reports.custom');
    Route::get('diseases/{disease}/history', [DiseaseController::class, 'history'])->name('diseases.history');
    Route::post('diseases', [DiseaseController::class, 'store'])->name('diseases.store');
    Route::post('diseases/create', [DiseaseController::class, 'store'])->name('diseases.create');
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role')->middleware('role:admin');
    Route::post('/users/{user}/toggle-permission', [AdminUserController::class, 'togglePermission'])->name('users.toggle-permission');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/transactions', [TransactionsController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionsController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transaction}/approve', [TransactionsController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/decline', [TransactionsController::class, 'reject'])->name('transactions.reject');
    Route::post('/transactions/{transaction}/destroy', [TransactionsController::class, 'destroy'])->name('transactions.destroy');

    Route::get('birds/trashed', [BirdsController::class, 'trashed'])->name('birds.trashed');
    Route::post('birds/{id}/restore', [BirdsController::class, 'restore'])->name('birds.restore');
    Route::get('/weather/fetch', [WeatherController::class, 'fetch'])->name('weather.fetch');
    
// Route::get('sales/pending-payments', [SalesController::class, 'pendingPayments'])->name('sales.pendingPayments');

    
});


Route::middleware(['web', 'auth'])->group(function () {
      Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('sales/{sale}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('sales/{sale}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');

    // invoice preview / download
    Route::get('sales/{sale}/invoice', [SalesController::class, 'invoice'])->name('sales.invoice');

    // record payment (AJAX-friendly)
    Route::post('sales/{sale}/record-payment', [SalesController::class, 'recordPayment'])->name('sales.recordPayment');

    // new: pending payments JSON endpoint (used by the modal)
    Route::get('sales/pending-json', [SalesController::class, 'pendingJson'])->name('sales.pendingJson');

    // Add any other sale related routes you need...
    Route::get('sales/{sale}/invoice/preview', [SalesController::class, 'invoicePreview'])
    ->name('sales.invoice.preview');
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

// Route::middleware(['web', 'auth'])->group(function () {
//     Route::get('/sales/pending-payments', [SalesController::class, 'pendingPayments'])->name('sales.pendingPayments');
//     Route::post('/sales/{sale}/payment', [SalesController::class, 'recordPayment'])->name('sales.recordPayment');
// });


// Route::middleware(['auth'])->group(function () {
// // Returns all unpaid (non-paid) sales as JSON. Optional ?status=pending|partially_paid|overdue
// Route::get('sales/pending-json', [SalesController::class, 'pendingJson'])->name('sales.pendingJson');


// // Returns a single sale as JSON (sale record + related customer + saleable summary)
// Route::get('sales/{sale}/json', [SalesController::class, 'json'])->name('sales.json');
// });