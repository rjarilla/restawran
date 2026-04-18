

<?php
use App\Http\Controllers\Admin\AuthController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\UserProfPrivilegesController;
use App\Http\Controllers\Admin\ProductInventoryController;
// ProductInventory CRUD routes
Route::get('/admin/productinventory', [ProductInventoryController::class, 'index'])->name('admin.productinventory.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('productinventory', ProductInventoryController::class)->except(['index']);
});
// Routes for ecommerce website
Route::get('/', function () { return view('index'); });
Route::get('/index', function () { return view('index'); });

// Routes for dashboard
Route::get('/admin/index', function () {
    if(session('user_id')) {
        return view('admin/index');
    } else {
        return view('admin/signin');
    }
});
Route::get('/admin/login', function () {
    return view('admin/login');
});

Route::get('/admin/signin', function () {
    return view('admin/signin');
});

Route::get('/admin', function () {
    if(session('user_id')) {
        return redirect('admin/index');
    } else {
        return redirect('admin/signin');
    }
});

Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::get('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Lookup CRUD routes
Route::get('/admin/lookup', [LookupController::class, 'index'])->name('admin.lookup.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('lookup', LookupController::class)->except(['index']);
});

// Product CRUD routes
Route::get('/admin/product', [ProductController::class, 'index'])->name('admin.product.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('product', ProductController::class)->except(['index']);
});

// Users CRUD routes
Route::get('/admin/users', [UsersController::class, 'index'])->name('admin.users.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UsersController::class)->except(['index']);
});

// UserProfile CRUD routes
Route::get('/admin/userprofile', [UserProfileController::class, 'index'])->name('admin.userprofile.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('userprofile', UserProfileController::class)->except(['index']);
});

// UserProfPrivileges CRUD routes
Route::get('/admin/userprofprivileges', [UserProfPrivilegesController::class, 'index'])->name('admin.userprofprivileges.index');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('userprofprivileges', UserProfPrivilegesController::class)->except(['index']);
});

// Orders default pages
Route::get('/admin/orders', function () {
    return view('admin.orders.index');
})->name('admin.orders.index');

// Reports default pages
Route::get('/admin/reports', function () {
    return view('admin.reports.index');
})->name('admin.reports.index');

// Payments default page
Route::get('/admin/payments', function () {
    return view('admin.payments.index');
})->name('admin.payments.index');

