@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Flight Performance Report')

@section('page-style')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
    }

    .header {
        font-weight: bold;
        background-color: #f4f4f4;
    }

    .location {
        font-size: 18px;
        font-weight: bold;
        background-color: #ffffcc;
        padding: 5px;
    }

    .week-header {
        font-weight: bold;
        text-align: left;
        padding: 5px;
    }
</style>
@endsection

@section('page-script')
<script src="{{asset('assets/js/flight-performance-report/report.js')}}"></script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Flight Performance Report
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif
@if(session('failure'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('failure') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible" role="alert">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h5>Flight Performance Report</h5>
    </div>
    <div class="card-body d-flex justify-content-center">
        <form id="flightPerformanceReportForm" class="w-75" method="POST"
            action="{{ route('reports.flight-performance-report.list') }}">
            @csrf

            <!-- First Row: Year, Start Week, End Week -->
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="operationalYear">Operational Year</label>
                    <select name="operational_year" id="operationalYear" class="form-select select2">
                        <option value="">-- Select Year --</option>
                        @foreach ($operationalYears as $operationalYear)
                        <option value="{{$operationalYear->id}}" @if(request('operational_year')==$operationalYear->id)
                            selected @endif >{{$operationalYear->year}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="startWeek">Start Week</label>
                    <select name="start_week" id="startWeek" class="form-select select2" @if(!request('start_week'))
                        disabled @endif>
                        @foreach ($startWeeks as $startWeek)
                        <option value="{{$startWeek->week}}" @if(request('start_week')==$startWeek->week)
                            selected @endif >Week {{$startWeek->week}} ({{$startWeek->start_date}} to
                            {{$startWeek->end_date}}) </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="endWeek">End Week</label>
                    <select name="end_week" id="endWeek" class="form-select select2" @if(!request('end_week')) disabled
                        @endif>
                        @foreach ($endWeeks as $endWeek)
                        <option value="{{$endWeek->week}}" @if(request('end_week')==$endWeek->week)
                            selected @endif >Week {{$endWeek->week}} ({{$endWeek->start_date}} to
                            {{$endWeek->end_date}})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Second Row: Zone, Customer, Flight -->
            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label" for="zone">Zone</label>
                    <select name="zone" id="zone" class="form-select select2" @if(!request('zone')) disabled @endif>
                        <option value="">-- Select Zone --</option>
                        <option value="all" @if(request('zone')==='all' ) selected @endif>All Zones</option>
                        @foreach ($zones as $zone)
                        <option value="{{$zone->id}}" @if(request('zone')==$zone->id) selected @endif >{{$zone->name}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="customer">Customer</label>
                    <select name="customer" id="customer" class="form-select select2" @if(!request('customer')) disabled
                        @endif>
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                        <option value="{{$customer->id}}" @if(request('customer')==$customer->id) selected @endif
                            >{{$customer->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="flight">Flight</label>
                    <select name="flight" id="flight" class="form-select select2" @if(!request('flight')) disabled
                        @endif>
                        <option value="">-- Select Flight --</option>
                        <option value="all" @if(request('flight')==='all' ) selected @endif>All Flights</option>
                        @foreach ($flights as $flight)
                        <option value="{{$flight->flight_number}}" @if(request('flight')==$flight->flight_number)
                            selected @endif
                            >{{$flight->flight_number}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Run Report Button -->
            <div class="row mt-3">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary" id="fprRunBtn" @if(!request('flight')) disabled
                        @endif>Run Report</button>
                </div>
            </div>
        </form>
    </div>

</div>

@if(count($finalData) > 0)
<div class="card mt-2">
    <div class="card-header text-center">
        <h5>Customer: {{ $customerToShow }}</h5>
        <h5>Weeks: {{ $weeksToShow }} </h5>
        <form action="{{ route('reports.flight-performance-report.export') }}" method="POST">
            @csrf
            <input type="hidden" name="export_operational_year" value="{{ request('operational_year') }}">
            <input type="hidden" name="export_start_week" value="{{ request('start_week') }}">
            <input type="hidden" name="export_end_week" value="{{ request('end_week') }}">
            <input type="hidden" name="export_zone" value="{{ request('zone') }}">
            <input type="hidden" name="export_customer" value="{{ request('customer') }}">
            <input type="hidden" name="export_flight" value="{{ request('flight') }}">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-file-excel me-2"></i> Export
            </button>
        </form>
    </div>
    <div class="card-body">
        @foreach($finalData as $key => $data)
        <h5>{{ $data['location'] }}</h5>
        <table>
            @foreach ($data['routes'] as $routeKey => $weeksData)
            @php
            $splitRoutes = explode(':', $routeKey);
            $forwardRoute = $splitRoutes[0];
            $reverseRoute = $splitRoutes[1] ?? '';
            $weeks = array_keys($weeksData);

            // Fetch first available Flight Number for Forward & Reverse routes
            $forwardFlightNumber = '-';
            $reverseFlightNumber = '-';

            foreach ($weeksData as $weekData) {
            foreach ($weekData as $flightData) {
            if (!empty($flightData['flights']) && isset($flightData['flights'][0]['flight'])) {
            $forwardFlightNumber = $flightData['flights'][0]['flight']['flight_number'];
            }
            if (!empty($flightData['reverse_flights']) && isset($flightData['reverse_flights'][0]['flight'])) {
            $reverseFlightNumber = $flightData['reverse_flights'][0]['flight']['flight_number'];
            }
            if ($forwardFlightNumber !== '-' && $reverseFlightNumber !== '-') {
            break 2; // Break out of both loops once flight numbers are found
            }
            }
            }
            @endphp
            <tr>
                {{-- Table Header --}}
                <td>
                    <table>
                        <tr class="header">
                            <th colspan="2"></th>
                            <th colspan="6">{{ $forwardRoute }}</th>
                            <th>Flight Number</th>
                            <th colspan="1">{{ $forwardFlightNumber }}</th>
                            <th colspan="1"></th>
                            <th colspan="6">{{ $reverseRoute }}</th>
                            <th>Flight Number</th>
                            <th colspan="1">{{ $reverseFlightNumber }}</th>
                        </tr>
                        <tr class="header">
                            <th>Day</th>
                            <th>Date</th>
                            <th>STD</th>
                            <th>ATD</th>
                            <th>+/- min</th>
                            <th>STA</th>
                            <th>ATA</th>
                            <th>+/- min</th>
                            <th>Weight</th>
                            <th>Remarks</th>
                            <th></th>
                            <th>STD</th>
                            <th>ATD</th>
                            <th>+/- min</th>
                            <th>STA</th>
                            <th>ATA</th>
                            <th>+/- min</th>
                            <th>Weight</th>
                            <th>Remarks</th>
                        </tr>

                        @foreach ($weeks as $week)
                        <tr>
                            <td colspan="20" class="week-header">Week {{ $week }}</td>
                        </tr>

                        @foreach ($weeksData[$week] as $day => $flightData)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day)->shortEnglishDayOfWeek }}</td>
                            <td>{{ \Carbon\Carbon::parse($day)->format('d') }}</td>

                            {{-- Forward Flight Data --}}
                            @if ($flightData['flights'])
                            @php $flight = $flightData['flights'][0]; @endphp
                            <td>{{ $flight['estimated_departure_time'] ?? '-' }}</td>
                            <td>{{ $flight['actual_departure_time'] ?? '-' }}</td>
                            <td>{{ $flight['departure_time_diff'] ?? '-' }}</td>
                            <td>{{ $flight['estimated_arrival_time'] ?? '-' }}</td>
                            <td>{{ $flight['actual_arrival_time'] ?? '-' }}</td>
                            <td>{{ $flight['arrival_time_diff'] ?? '-' }}</td>
                            <td>
                                @if (!empty($flight['schedule_flight_customers']))
                                @foreach ($flight['schedule_flight_customers'] as $customer)
                                {{ $customer['total_uplifted_weight'] }}<br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if (!empty($flight['schedule_flight_remarks']))
                                @foreach ($flight['schedule_flight_remarks'] as $remark)
                                {{ $remark['remark'] }}<br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>
                            <td></td>
                            @else
                            {{-- Empty forward flight row --}}
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td></td>
                            @endif

                            {{-- Reverse Flight Data --}}
                            @if ($flightData['reverse_flights'])
                            @php $flight = $flightData['reverse_flights'][0]; @endphp
                            <td>{{ $flight['estimated_departure_time'] ?? '-' }}</td>
                            <td>{{ $flight['actual_departure_time'] ?? '-' }}</td>
                            <td>{{ $flight['departure_time_diff'] ?? '-' }}</td>
                            <td>{{ $flight['estimated_arrival_time'] ?? '-' }}</td>
                            <td>{{ $flight['actual_arrival_time'] ?? '-' }}</td>
                            <td>{{ $flight['arrival_time_diff'] ?? '-' }}</td>
                            <td>
                                @if (!empty($flight['schedule_flight_customers']))
                                @foreach ($flight['schedule_flight_customers'] as $customer)
                                {{ $customer['total_uplifted_weight'] }}<br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if (!empty($flight['schedule_flight_remarks']))
                                @foreach ($flight['schedule_flight_remarks'] as $remark)
                                {{ $remark['remark'] }}<br>
                                @endforeach
                                @else
                                -
                                @endif
                            </td>
                            @else
                            {{-- Empty reverse flight row --}}
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            @endif
                        </tr>
                        @endforeach
                        @endforeach
                    </table>
                </td>
            </tr>
            @endforeach
        </table>
        @endforeach
    </div>

</div>
@endif

@endsection