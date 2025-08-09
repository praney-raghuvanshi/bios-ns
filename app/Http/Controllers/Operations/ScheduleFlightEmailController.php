<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Mail\FlightUpdate;
use App\Models\Schedule;
use App\Models\ScheduleFlight;
use App\Models\ScheduleFlightCustomer;
use App\Models\ScheduleFlightEmail;
use App\Models\ScheduleFlightRemark;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ScheduleFlightEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function preview(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight, ScheduleFlightCustomer $scheduleFlightCustomer)
    {
        try {

            $data = $serviceRemarks = [];

            $isRemarkIncluded = $request->input('isRemarkIncluded') === 'true' ? 1 : 0;

            // Get Flight Location
            $locationId = $scheduleFlight->flight->location_id;

            // Get Customer Emails for that location

            $customerEmails = $scheduleFlightCustomer->load(['customer.emails.locations'])
                ->customer
                ->emails
                ->filter(function ($email) use ($locationId) {
                    return $email->locations->contains('id', $locationId);
                });

            foreach ($customerEmails as $customerEmail) {
                $data[] = [
                    'name' => $customerEmail->name ?? '',
                    'email' => $customerEmail->email
                ];
            }

            if ($isRemarkIncluded) {
                $serviceRemarks = ScheduleFlightRemark::where('customer_id', $scheduleFlightCustomer->customer_id)->where('is_fpr', 1)->orderBy('id', 'desc')->pluck('remark');
            }
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return view('_partials._modals.schedule.flight.email.preview', compact('schedule', 'scheduleFlight', 'scheduleFlightCustomer', 'data', 'serviceRemarks'));
    }

    public function send(Request $request, Schedule $schedule, ScheduleFlight $scheduleFlight)
    {
        try {
            $scheduleFlightCustomerIds = $request->input('send_email');
            $scheduleFlightCustomerIdsForRemarks = $request->input('include_remark');

            $scheduleFlightCustomers = ScheduleFlightCustomer::whereIn('id', $scheduleFlightCustomerIds)->get();
            foreach ($scheduleFlightCustomers as $scheduleFlightCustomer) {

                $emailAddresses = [];

                // Get Flight Location
                $locationId = $scheduleFlight->flight->location_id;

                // Get Customer Emails for that location

                $customerEmails = $scheduleFlightCustomer->load(['customer.emails.locations'])
                    ->customer
                    ->emails
                    ->filter(function ($email) use ($locationId) {
                        return $email->locations->contains('id', $locationId);
                    });

                foreach ($customerEmails as $customerEmail) {
                    $emailAddresses[] = $customerEmail->email;
                }

                $data = [
                    'flight_number' => $scheduleFlight->flight->flight_number,
                    'date' => Carbon::parse($schedule->date)->format('d F Y'),
                    'route' => $scheduleFlight->flight->fromAirport->iata . ' - ' . $scheduleFlight->flight->toAirport->iata,
                    'std' => Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i'),
                    'etd' => optional($scheduleFlight->estimated_departure_time) ?
                        Carbon::parse($scheduleFlight->estimated_departure_time)->format('H:i') : 'None',
                    'atd' => optional($scheduleFlight->actual_departure_time) ?
                        Carbon::parse($scheduleFlight->actual_departure_time)->format('H:i') : 'None',
                    'departure_diff' => $scheduleFlight->departure_time_diff,
                    'sta' => Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i'),
                    'eta' => optional($scheduleFlight->estimated_arrival_time) ?
                        Carbon::parse($scheduleFlight->estimated_arrival_time)->format('H:i') : 'None',
                    'ata' => optional($scheduleFlight->actual_arrival_time) ?
                        Carbon::parse($scheduleFlight->actual_arrival_time)->format('H:i') : 'None',
                    'uplifted' => $scheduleFlightCustomer->total_uplifted_weight ?? 0,
                    'offloaded' => $scheduleFlight->total_offloaded_weight ?? 0
                ];

                $subject = 'Bridges WW Update for Flight ' . $data['flight_number'] . ' on ' . $data['date'];

                if (in_array($scheduleFlightCustomer->id, $scheduleFlightCustomerIdsForRemarks)) {
                    $data['service_remarks'] = ScheduleFlightRemark::where('customer_id', $scheduleFlightCustomer->customer_id)->where('is_fpr', 1)->orderBy('id', 'desc')->pluck('remark');
                } else {
                    $data['service_remarks'] = [];
                }

                if (count($emailAddresses) > 0) {
                    Mail::to($emailAddresses)->send(new FlightUpdate($subject, $data));
                    ScheduleFlightEmail::create([
                        'schedule_flight_id' => $scheduleFlight->id,
                        'customer_id' => $scheduleFlightCustomer->customer_id,
                        'to' => implode(",", $emailAddresses),
                        'subject' => $subject,
                        'content' => json_encode($data),
                        'status' => 'sent',
                        'added_by' => Auth::id()
                    ]);
                }
            }
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('flight-operations.schedule.flight.email', [$schedule, $scheduleFlight])->with('success', 'Schedule Flight Emails sent successfully.');
    }
}
