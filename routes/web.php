<?php

use App\Http\Controllers\Administration\ActivityLogController;
use App\Http\Controllers\Administration\GroupController;
use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Maintenance\AircraftController;
use App\Http\Controllers\Maintenance\AirportController;
use App\Http\Controllers\Maintenance\CustomerController;
use App\Http\Controllers\Maintenance\CustomerEmailController;
use App\Http\Controllers\Maintenance\FlightController;
use App\Http\Controllers\Maintenance\LocationController;
use App\Http\Controllers\Maintenance\OperationalCalendarController;
use App\Http\Controllers\Maintenance\ProductController;
use App\Http\Controllers\Maintenance\ZoneController;
use App\Http\Controllers\Operations\ScheduleController;
use App\Http\Controllers\Operations\ScheduleFlightController;
use App\Http\Controllers\Operations\ScheduleFlightCustomerController;
use App\Http\Controllers\Operations\ScheduleFlightCustomerProductController;
use App\Http\Controllers\Operations\ScheduleFlightCustomerShipmentController;
use App\Http\Controllers\Operations\ScheduleFlightEmailController;
use App\Http\Controllers\Operations\ScheduleFlightRemarkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\BillingExtractController;
use App\Http\Controllers\Reports\FlightPerformanceReportController;
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

    // - Operational Calendar Routes
    Route::name('operational-calendar.')->controller(OperationalCalendarController::class)->group(function () {
        Route::get('/operational-calendars', 'index')->name('list')->can('view operational-calendars');
        Route::post('/operational-calendars', 'store')->name('store')->can('add operational-calendars');
        // Route::get('/operational-calendars/{operationalCalendar}/edit', 'edit')->name('edit')->can('edit operational-calendars');
        // Route::post('/operational-calendars/{operationalCalendar}/update', 'update')->name('update')->can('edit operational-calendars');
        Route::delete('/operational-calendars/{operationalCalendar}', 'destroy')->name('destroy')->can('delete operational-calendars');
    });

    // - Aircraft Routes
    Route::name('aircraft.')->controller(AircraftController::class)->group(function () {
        Route::get('/aircrafts', 'index')->name('list')->can('view aircrafts');
        Route::post('/aircrafts', 'store')->name('store')->can('add aircrafts');
        Route::get('/aircrafts/{aircraft}/edit', 'edit')->name('edit')->can('edit aircrafts');
        Route::post('/aircrafts/{aircraft}/update', 'update')->name('update')->can('edit aircrafts');
        Route::delete('/aircrafts/{aircraft}', 'destroy')->name('destroy')->can('delete aircrafts');
    });

    // - Flight Routes

    Route::get('/flights', function () {
        $location = session('location');

        // If location exists and not in the query, redirect with location in URL
        if ($location && !request()->has('location')) {
            return redirect()->to(request()->fullUrlWithQuery(['location' => $location]));
        }

        return app()->call('App\Http\Controllers\Maintenance\FlightController@index');
    })->name('flight.list')->can('view flights');

    Route::name('flight.')->controller(FlightController::class)->group(function () {

        Route::post('/flights', 'store')->name('store')->can('add flights');
        Route::get('/flights/{flightPairId}', 'show')->name('show')->can('view flights');
        Route::get('/flights/{flight}/edit', 'edit')->name('edit')->can('edit flights');
        Route::post('/flights/{flight}/update', 'update')->name('update')->can('edit flights');
        Route::delete('/flights/{flight}', 'destroy')->name('destroy')->can('delete flights');

        Route::get('flights/{flight}/day/{flightDay}/inactive', 'inactiveFlightDay')->name('day.inactive')->can('edit flights');
        Route::get('flights/{flight}/day/{flightDay}/active', 'activeFlightDay')->name('day.active')->can('edit flights');
        Route::delete('/flights/{flight}/day/{flightDay}', 'flightDayDestroy')->name('day.destroy')->can('delete flights');

        Route::get('flights/{flight}/day/{flightDay}/customers', 'flightDayCustomers')->name('day.customer')->can('view customers');
        Route::post('/flights/{flight}/day/{flightDay}/manage-customers', 'manageFlightDayCustomers')->name('day.manage-customers')->can('edit customers');

        Route::get('/flights/{flightPairId}/clone', 'clone')->name('clone')->can('edit flights');
    });
});

Route::middleware(['auth', 'verified'])->prefix('flight-operations')->name('flight-operations.')->group(function () {

    // - Schedule Routes
    Route::name('schedule.')->controller(ScheduleController::class)->group(function () {
        Route::get('/schedules', 'index')->name('list')->can('view schedules');
        Route::get('/schedules/create', 'create')->name('create')->can('add schedules');
        Route::post('/schedules/confirm', 'confirm')->name('confirm')->can('add schedules');
        Route::post('/schedules', 'store')->name('store')->can('add schedules');
        Route::get('/schedules/{schedule}', 'show')->name('show')->can('view schedules');
        Route::delete('/schedules/{schedule}', 'destroy')->name('destroy')->can('delete schedules');
        Route::get('/schedules/{schedule}/manual/flights/list', 'manualList')->name('manual.list')->can('view schedules');
        Route::get('/schedules/{schedule}/manual/flights/contingency', 'contingency')->name('manual.contingency')->can('view schedules');
        Route::post('/schedules/{schedule}/manual/flights/store', 'manualStore')->name('manual.store')->can('add schedules');
        Route::post('/schedules/{schedule}/manual/flights/contingency/store', 'contingencyStore')->name('manual.contingency.store')->can('add schedules');


        // Schedule Flight Routes
        Route::name('flight.')->controller(ScheduleFlightController::class)->group(function () {
            Route::get('/schedules/{schedule}/flights/{scheduleFlight}', 'show')->name('show')->can('view schedules');
            Route::post('/schedules/{schedule}/flights/{scheduleFlight}/update', 'update')->name('update')->can('edit schedules');
            Route::get('/schedules/{schedule}/flights/{scheduleFlight}/mark-complete', 'markComplete')->name('mark-complete')->can('add schedules');
            Route::get('/schedules/{schedule}/flights/{scheduleFlight}/cancel', 'cancel')->name('cancel')->can('delete schedules');
            Route::get('/schedules/{schedule}/flights/{scheduleFlight}/emails', 'scheduleFlightEmails')->name('email')->can('view schedules');

            // Schedule Flight Remark Routes
            Route::name('remark.')->controller(ScheduleFlightRemarkController::class)->group(function () {
                Route::post('/schedules/{schedule}/flights/{scheduleFlight}/remarks', 'store')->name('store')->can('add schedules');
                Route::get('/schedules/{schedule}/flights/{scheduleFlight}/remarks/{scheduleFlightRemark}/edit', 'edit')->name('edit')->can('edit schedules');
                Route::post('/schedules/{schedule}/flights/{scheduleFlight}/remarks/{scheduleFlightRemark}', 'update')->name('update')->can('edit schedules');
                Route::delete('/schedules/{schedule}/flights/{scheduleFlight}/remarks/{scheduleFlightRemark}', 'destroy')->name('destroy')->can('delete schedules');
            });

            // Schedule Flight Email Routes
            Route::name('email.')->controller(ScheduleFlightEmailController::class)->group(function () {
                Route::get('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/email-preview', 'preview')->name('preview')->can('view schedules');
                Route::get('/schedules/{schedule}/flights/{scheduleFlight}/send-email', 'send')->name('send')->can('add schedules');
            });

            // Schedule Flight Customer Routes
            Route::name('customer.')->controller(ScheduleFlightCustomerController::class)->group(function () {
                Route::get('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}', 'show')->name('show')->can('view schedules');
                Route::post('/schedules/{schedule}/flights/{scheduleFlight}/customers', 'store')->name('store')->can('add schedules');

                // Schedule Flight Customer Product Routes
                Route::name('product.')->controller(ScheduleFlightCustomerProductController::class)->group(function () {
                    Route::post('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/products', 'store')->name('store')->can('add schedules');
                    Route::get('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/products/{scheduleFlightCustomerProduct}/edit', 'edit')->name('edit')->can('edit schedules');
                    Route::post('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/products/{scheduleFlightCustomerProduct}', 'update')->name('update')->can('edit schedules');
                    Route::delete('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/products/{scheduleFlightCustomerProduct}', 'destroy')->name('destroy')->can('delete schedules');
                });

                // Schedule Flight Customer Shipment (AWB) Routes
                Route::name('shipment.')->controller(ScheduleFlightCustomerShipmentController::class)->group(function () {
                    Route::post('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/shipments', 'store')->name('store')->can('add schedules');
                    Route::get('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/shipments/{scheduleFlightCustomerShipment}/edit', 'edit')->name('edit')->can('edit schedules');
                    Route::post('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/shipments/{scheduleFlightCustomerShipment}', 'update')->name('update')->can('edit schedules');
                    Route::delete('/schedules/{schedule}/flights/{scheduleFlight}/customers/{scheduleFlightCustomer}/shipments/{scheduleFlightCustomerShipment}', 'destroy')->name('destroy')->can('delete schedules');
                });
            });
        });
    });
});

Route::middleware(['auth', 'verified'])->prefix('reports')->name('reports.')->group(function () {

    // - Flight Performance Routes
    Route::name('flight-performance-report.')->controller(FlightPerformanceReportController::class)->group(function () {
        Route::match(['get', 'post'], '/flight-performance-report', 'index')->name('list')->can('view flight-performance-report');
        Route::post('/flight-performance-report/export', 'export')->name('export')->can('view flight-performance-report');
    });

    // - Billing Extract Routes
    Route::name('billing-extract.')->controller(BillingExtractController::class)->group(function () {
        Route::match(['get', 'post'], '/billing-extract', 'index')->name('list')->can('view billing-extract');
    });
});

// Routes for AJAX
Route::middleware('auth')->group(function () {
    Route::post('manageMenuFavourites', [UserController::class, 'manageMenuFavourites']);
    Route::post('checkAwbForScheduleFlightCustomer', [ScheduleFlightCustomerShipmentController::class, 'checkAwbForScheduleFlightCustomer']);
    Route::post('checkFlight', [FlightController::class, 'checkFlight']);

    Route::post('getWeeksForOperationalYear', [FlightPerformanceReportController::class, 'getWeeksForOperationalYear']);
    Route::post('getFlightsForCustomer', [FlightPerformanceReportController::class, 'getFlightsForCustomer']);
});

require __DIR__ . '/auth.php';
