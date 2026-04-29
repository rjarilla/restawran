<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// CONTROLLERS
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserProfPrivilegesController;
use App\Http\Controllers\Admin\ProductInventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportsController;

/*
|--------------------------------------------------------------------------
| DEBUG ROUTES (REMOVE IN PRODUCTION)
|--------------------------------------------------------------------------
*/

Route::get('/test-customers', fn () => 'CUSTOMER ROUTE WORKS');

Route::get('/debug-db', fn () => DB::connection()->getDatabaseName());

/*
|--------------------------------------------------------------------------
| FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('index'));
Route::get('/index', fn () => view('index'));

/*
|--------------------------------------------------------------------------
| ORDER MODULE
|--------------------------------------------------------------------------
*/

Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

/*
|--------------------------------------------------------------------------
| PAYMENT MODULE
|--------------------------------------------------------------------------
*/

Route::get('/payment', [PaymentController::class, 'showPaymentMethod'])->name('payment.method');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

/*
|--------------------------------------------------------------------------
| ADMIN AUTH (CLEANED)
|--------------------------------------------------------------------------
*/

// Always redirect admin root to signin
Route::get('/admin', fn () => redirect()->route('admin.login.form'));

// ONLY ONE LOGIN PAGE (IMPORTANT)
Route::get('/admin/signin', [AuthController::class, 'showLoginForm'])->name('admin.login.form');

// LOGIN POST
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');

// LOGOUT
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/index', [DashboardController::class, 'index'])
    ->name('admin.index');

/*
|--------------------------------------------------------------------------
| ADMIN MODULES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('productinventory', ProductInventoryController::class);
    Route::resource('lookup', LookupController::class);
    Route::resource('product', ProductController::class);
    Route::resource('users', UsersController::class);
    Route::resource('userprofile', UserProfileController::class);
    Route::resource('userprofprivileges', UserProfPrivilegesController::class);

    Route::resource('customers', CustomerController::class);
});

/*
|--------------------------------------------------------------------------
| STATIC ADMIN PAGES
|--------------------------------------------------------------------------
*/

Route::get('/admin/orders', fn () => view('admin.orders.index'))
    ->name('admin.orders.index');

Route::get('/admin/reports', [ReportsController::class, 'index'])
    ->name('admin.reports.index');

/*
|--------------------------------------------------------------------------
| ADMIN PAYMENTS
|--------------------------------------------------------------------------
*/

Route::get('/admin/payments', [AdminPaymentController::class, 'index'])
    ->name('admin.payments.index');

Route::get('/admin/payments/{id}', [AdminPaymentController::class, 'show'])
    ->name('admin.payments.show');

Route::delete('/admin/payments/{id}', [AdminPaymentController::class, 'destroy'])
    ->name('admin.payments.destroy');