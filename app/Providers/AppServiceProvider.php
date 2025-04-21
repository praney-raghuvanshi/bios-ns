<?php

namespace App\Providers;

use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightCustomer;
use App\Models\ScheduleFlightCustomerProduct;
use App\Models\ScheduleFlightCustomerShipment;
use App\Models\ScheduleFlightEmail;
use App\Models\ScheduleFlightRemark;
use App\Observers\ScheduleFlightCustomerObserver;
use App\Observers\ScheduleFlightCustomerProductObserver;
use App\Observers\ScheduleFlightCustomerShipmentObserver;
use App\Observers\ScheduleFlightEmailObserver;
use App\Observers\ScheduleFlightObserver;
use App\Observers\ScheduleFlightRemarkObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    ScheduleFlight::observe(ScheduleFlightObserver::class);
    ScheduleFlightCustomer::observe(ScheduleFlightCustomerObserver::class);
    ScheduleFlightCustomerProduct::observe(ScheduleFlightCustomerProductObserver::class);
    ScheduleFlightCustomerShipment::observe(ScheduleFlightCustomerShipmentObserver::class);
    //ScheduleFlightEmail::observe(ScheduleFlightEmailObserver::class);
    //ScheduleFlightRemark::observe(ScheduleFlightRemarkObserver::class);
  }
}
