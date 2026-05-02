<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserProfPrivilegesController;
use App\Http\Controllers\Admin\ProductInventoryController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\InventoryMovementReportController;

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES (REMOVE LATER IN PRODUCTION)
|--------------------------------------------------------------------------
*/

Route::get('/test-customers', function () {
    return 'CUSTOMER ROUTE WORKS';
});

Route::get('/debug-db', function () {
    return DB::connection()->getDatabaseName();
});

/*
|--------------------------------------------------------------------------
| FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/index', [IndexController::class, 'index'])->name('index');
//Route::redirect('/public', '/');
//Route::redirect('/public/', '/');
//Route::redirect('/public/{any}', '/', 301)->where('any', '.*');

/*
|--------------------------------------------------------------------------
| ORDER MODULE
|--------------------------------------------------------------------------
*/

Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

/*
|--------------------------------------------------------------------------
| PAYMENT MODULE (FRONTEND)
|--------------------------------------------------------------------------
*/

Route::get('/payment', [PaymentController::class, 'showPaymentMethod'])->name('payment.method');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {
    return session('user_id') ? redirect('admin/index') : redirect('admin/signin');
});

Route::get('/admin/index', [DashboardController::class, 'index'])->name('admin.index');

Route::get('/admin/login', fn () => view('admin/login'));
Route::get('/admin/signin', fn () => view('admin/signin'))->name('admin.signin');

Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN MODULES (CRUD)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['check.admin.session'])->group(function () {
    
    Route::resource('lookup', LookupController::class);
    Route::resource('product', ProductController::class);
    Route::resource('productinventory', ProductInventoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UsersController::class);
    Route::resource('userprofile', UserProfileController::class);
    Route::resource('userprofprivileges', UserProfPrivilegesController::class);
    Route::resource('orders', OrdersController::class);
    Route::resource('payments', AdminPaymentController::class);
    Route::resource('reports', ReportsController::class);
    
    Route::get('orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::delete('payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
});

/*
|--------------------------------------------------------------------------
| STATIC ADMIN PAGES
|--------------------------------------------------------------------------
*/

Route::get('/admin/orders', [OrdersController::class, 'index'])->name('admin.orders.index');
Route::get('/admin/reports', [ReportsController::class, 'index'])->name('admin.reports.index');
Route::get('/admin/reports/inventory-movement', [InventoryMovementReportController::class, 'index'])->name('admin.reports.inventory_movement');

/*
|--------------------------------------------------------------------------
| ADMIN PAYMENTS
|--------------------------------------------------------------------------
*/

Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
Route::get('/admin/payments/{id}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
Route::delete('/admin/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('admin.payments.destroy');
