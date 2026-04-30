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

Route::get('/test-customers', fn () => 'CUSTOMER ROUTE WORKS');
Route::get('/debug-db', fn () => DB::connection()->getDatabaseName());

Route::get('/', [IndexController::class, 'index']);
Route::get('/index', [IndexController::class, 'index']);

Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

Route::get('/payment', [PaymentController::class, 'showPaymentMethod'])->name('payment.method');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('/admin', fn () => redirect()->route('admin.login.form'));
Route::get('/admin/signin', [AuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::get('/admin/login', fn () => redirect()->route('admin.login.form'));
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::get('/admin/index', [DashboardController::class, 'index'])
    ->name('admin.index');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('productinventory', ProductInventoryController::class);
    Route::resource('lookup', LookupController::class);
    Route::resource('product', ProductController::class);
    Route::resource('users', UsersController::class);
    Route::resource('userprofile', UserProfileController::class);
    Route::resource('userprofprivileges', UserProfPrivilegesController::class);
    Route::resource('customers', CustomerController::class);

    Route::get('orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::delete('payments/{id}', [AdminPaymentController::class, 'destroy'])->name('payments.destroy');
});

