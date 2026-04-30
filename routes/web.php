<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AuthController;
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

Route::get('/admin/index', function () {
    return session('user_id') ? view('admin/index') : view('admin/signin');
});

Route::get('/admin/login', fn () => view('admin/login'));
Route::get('/admin/signin', fn () => view('admin/signin'));

Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN MODULES (CRUD)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('productinventory', ProductInventoryController::class);
    Route::resource('lookup', LookupController::class);
    Route::resource('product', ProductController::class);
    Route::resource('users', UsersController::class);
    Route::resource('userprofile', UserProfileController::class);
    Route::resource('userprofprivileges', UserProfPrivilegesController::class);

    // CUSTOMER CRUD
    Route::resource('customers', CustomerController::class);
});

/*
|--------------------------------------------------------------------------
| STATIC ADMIN PAGES
|--------------------------------------------------------------------------
*/

Route::get('/admin/orders', fn () => view('admin.orders.index'))->name('admin.orders.index');
Route::get('/admin/reports', fn () => view('admin.reports.index'))->name('admin.reports.index');
Route::get('/admin/reports/inventory-movement', [InventoryMovementReportController::class, 'index'])->name('admin.reports.inventory_movement');


/*
|--------------------------------------------------------------------------
| ADMIN PAYMENTS
|--------------------------------------------------------------------------
*/

Route::get('/admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
Route::get('/admin/payments/{id}', [AdminPaymentController::class, 'show'])->name('admin.payments.show');
Route::delete('/admin/payments/{id}', [AdminPaymentController::class, 'destroy'])->name('admin.payments.destroy');