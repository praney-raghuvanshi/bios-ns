<?php

use App\Http\Controllers\Administration\ActivityLogController;
use App\Http\Controllers\Administration\GroupController;
use App\Http\Controllers\Administration\UserController;
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

// Routes for AJAX
Route::middleware('auth')->group(function () {
    Route::post('manageMenuFavourites', [UserController::class, 'manageMenuFavourites']);
});

require __DIR__ . '/auth.php';
