<?php

use App\Http\Controllers\Administration\ActivityLogController;
use App\Http\Controllers\Administration\GroupController;
use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Maintenance\AirportController;
use App\Http\Controllers\Maintenance\CustomerController;
use App\Http\Controllers\Maintenance\CustomerEmailController;
use App\Http\Controllers\Maintenance\LocationController;
use App\Http\Controllers\Maintenance\ProductController;
use App\Http\Controllers\Maintenance\ZoneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'group.role:dashboard-view'])->name('dashboard');

Route::middleware(['auth', 'verified', 'group.role:search-view'])->controller(SearchController::class)->group(function () {
    Route::get('/search', 'search')->name('search');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/image/update', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::post('/profile/security/update', [ProfileController::class, 'updateSecurity'])->name('profile.security.update');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('administration')->name('administration.')->group(function () {

    // - Group Routes
    Route::name('group.')->controller(GroupController::class)->group(function () {
        Route::get('/groups', 'index')->name('list')->can('view groups');
        Route::post('/groups', 'store')->name('store')->can('add groups');
        Route::get('/groups/{group}', 'show')->name('show')->can('view groups');
        Route::post('/groups/{group}/update', 'update')->name('update')->can('edit groups');

        Route::post('/groups/{group}/manage-users', 'manageGroupUsers')->name('manage-users')->can('add users');
        Route::post('/groups/{group}/manage-roles', 'manageGroupRoles')->name('manage-roles')->can('add roles');
    });

    // - User Routes
    Route::name('user.')->controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('list')->can('view users');
        Route::post('/users', 'store')->name('store')->can('add users');
        Route::get('/users/{user}/edit', 'edit')->name('edit')->can('edit users');
        Route::post('/users/{user}/update', 'update')->name('update')->can('edit users');
    });

    // - BIOS Audit Routes
    Route::name('bios-audit.')->controller(ActivityLogController::class)->group(function () {
        Route::get('/bios-audit', 'index')->name('list')->can('view bios-audit');
    });
});

Route::middleware(['auth', 'verified'])->prefix('maintenance')->name('maintenance.')->group(function () {

    // - Zone Routes
    Route::name('zone.')->controller(ZoneController::class)->group(function () {
        Route::get('/zones', 'index')->name('list')->can('view zones');
        Route::post('/zones', 'store')->name('store')->can('add zones');
        Route::get('/zones/{zone}/edit', 'edit')->name('edit')->can('edit zones');
        Route::post('/zones/{zone}/update', 'update')->name('update')->can('edit zones');
        Route::delete('/zones/{zone}', 'destroy')->name('destroy')->can('delete zones');
    });

    // - Location Routes
    Route::name('location.')->controller(LocationController::class)->group(function () {
        Route::get('/locations', 'index')->name('list')->can('view locations');
        Route::post('/locations', 'store')->name('store')->can('add locations');
        Route::get('/locations/{location}/edit', 'edit')->name('edit')->can('edit locations');
        Route::post('/locations/{location}/update', 'update')->name('update')->can('edit locations');
        Route::delete('/locations/{location}', 'destroy')->name('destroy')->can('delete locations');
    });

    // - Airport Routes
    Route::name('airport.')->controller(AirportController::class)->group(function () {
        Route::get('/airports', 'index')->name('list')->can('view airports');
        Route::post('/airports', 'store')->name('store')->can('add airports');
        Route::get('/airports/{airport}/edit', 'edit')->name('edit')->can('edit airports');
        Route::post('/airports/{airport}/update', 'update')->name('update')->can('edit airports');
        Route::delete('/airports/{airport}', 'destroy')->name('destroy')->can('delete airports');
    });

    // - Product Routes
    Route::name('product.')->controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('list')->can('view products');
        Route::post('/products', 'store')->name('store')->can('add products');
        Route::get('/products/{product}/edit', 'edit')->name('edit')->can('edit products');
        Route::post('/products/{product}/update', 'update')->name('update')->can('edit products');
        Route::delete('/products/{product}', 'destroy')->name('destroy')->can('delete products');
    });

    // - Customer Routes
    Route::name('customer.')->controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index')->name('list')->can('view customers');
        Route::post('/customers', 'store')->name('store')->can('add customers');
        Route::get('/customers/{customer}', 'show')->name('show')->can('view customers');
        Route::get('/customers/{customer}/edit', 'edit')->name('edit')->can('edit customers');
        Route::post('/customers/{customer}/update', 'update')->name('update')->can('edit customers');
        Route::delete('/customers/{customer}', 'destroy')->name('destroy')->can('delete customers');

        Route::post('/customers/{customer}/manage-products', 'manageCustomerProducts')->name('manage-products')->can('add customers');
    });

    // - Customer Email Routes
    Route::name('customer.email.')->controller(CustomerEmailController::class)->group(function () {
        Route::get('/customers/{customer}/emails', 'index')->name('list')->can('view customers');
        Route::post('/customers/{customer}/emails', 'store')->name('store')->can('add customers');
        Route::get('/customers/{customer}/emails/{customerEmail}/edit', 'edit')->name('edit')->can('edit customers');
        Route::post('/customers/{customer}/emails/{customerEmail}/update', 'update')->name('update')->can('edit customers');
        Route::delete('/customers/{customer}/emails/{customerEmail}', 'destroy')->name('destroy')->can('delete customers');
    });
});

// Routes for AJAX
Route::middleware('auth')->group(function () {
    Route::post('manageMenuFavourites', [UserController::class, 'manageMenuFavourites']);
});

require __DIR__ . '/auth.php';
