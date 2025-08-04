@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Schedule Detail')

@section('page-script')

@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Schedule Detail
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
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div class="align-self-center">
            <span class="btn btn-info">
                Flight Summary: &nbsp;<strong> {{ \Carbon\Carbon::parse($schedule->date)->format('l d F Y') }}</strong>
            </span>
        </div>
        <a href="{{ route('flight-operations.schedule.list') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Schedule List
        </a>
    </div>
</div>

@can('view schedules')

<div class="card mt-3">
    <div class="card-header text-center p-2">
        <span class="badge bg-success me-2">On Time/Early Flight</span>
        <span class="badge bg-danger">Late Flight</span>
    </div>
    <div class="card-body p-2 align-items-center text-center">
        @if($schedule->scheduleFlights->isNotEmpty())
        <table class="table table-bordered table-sm text-center">
            <thead class="bg-secondary">
                <tr>
                    <th class="p-1">Flight</th>
                    <th class="p-1">From</th>
                    <th class="p-1">To</th>
                    <th class="p-1">STD</th>
                    <th class="p-1">ETD</th>
                    <th class="p-1">ATD</th>
                    <th class="p-1">+/- Min</th>
                    <th class="p-1">STA</th>
                    <th class="p-1">ETA</th>
                    <th class="p-1">ATA</th>
                    <th class="p-1">+/- Min</th>
                    <th class="p-1">Uplifted</th>
                    <th class="p-1">Aircraft</th>
                    <th class="p-1">Registration</th>
                    <th class="p-1">Utilisation</th>
                    <th class="p-1">Off Loaded</th>
                    <th class="p-1">Status</th>
                    <th class="p-1">Remarks</th>
                    <th class="p-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedule->scheduleFlights as $scheduleFlight)
                @php

                $now = \Carbon\Carbon::now('Europe/Berlin');
                $stdEtd = \Carbon\Carbon::parse($scheduleFlight->estimated_departure_time ??
                $scheduleFlight->flight->departure_time, 'Europe/Berlin'); // use ETD if available
                $eta = \Carbon\Carbon::parse($scheduleFlight->estimated_arrival_time ??
                $scheduleFlight->flight->arrival_time, 'Europe/Berlin');
                // use ETA if available

                // Departure color logic
                $departureClass = '';
                if(is_null($scheduleFlight->actual_departure_time) && $scheduleFlight->status === 1) {
                if ($now->gte($stdEtd->copy()->addMinutes(15))) {
                $departureClass = 'bg-danger-subtle'; // Red after 15 min
                } elseif ($now->gte($stdEtd)) {
                $departureClass = 'bg-purple-subtle'; // Purple at STD/ETD
                }
                }

                // Arrival color logic (similar to departure)
                $arrivalClass = '';
                if(is_null($scheduleFlight->actual_arrival_time) && $scheduleFlight->status === 1) {
                if ($now->gte($eta->copy()->addMinutes(15))) {
                $arrivalClass = 'bg-danger-subtle'; // Red after 15 min
                } elseif ($now->gte($eta)) {
                $arrivalClass = 'bg-purple-subtle'; // Purple at ETA
                }
                }
                @endphp
                <tr class="{{ $departureClass }} {{ $arrivalClass }}">
                    <td class="p-1">{{ $scheduleFlight->flight->flight_number }}</td>
                    <td class="p-1">{{ $scheduleFlight->flight->fromAirport->iata ?? '' }}</td>
                    <td class="p-1">{{ $scheduleFlight->flight->toAirport->iata ?? '' }}</td>
                    <td class="p-1">{{ \Carbon\Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i') }}
                        <br>({{ $scheduleFlight->flight->departure_time_local }})
                    </td>
                    <td class="p-1">{{ $scheduleFlight->formatted_etd }} <br> {{ !is_null($scheduleFlight->etd_local) ?
                        '('.$scheduleFlight->etd_local.')' : '' }}</td>
                    <td class="p-1">{{ $scheduleFlight->formatted_atd }} <br> {{ !is_null($scheduleFlight->atd_local) ?
                        '('.$scheduleFlight->atd_local.')' : '' }}</td>
                    <td class="p-1">
                        @if(!is_null($scheduleFlight->departure_time_diff))
                        @if($scheduleFlight->departure_time_diff >= 0)
                        <span class="badge bg-success">- {{ $scheduleFlight->formatted_departure_time_diff }}</span>
                        @else
                        <span class="badge bg-danger">+ {{ $scheduleFlight->formatted_departure_time_diff }}</span>
                        @endif
                        @endif
                    </td>
                    <td class="p-1">{{ \Carbon\Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i') }}
                        <br>
                        ({{ $scheduleFlight->flight->arrival_time_local }})
                    </td>
                    <td class="p-1">{{ $scheduleFlight->formatted_eta }} <br> {{ !is_null($scheduleFlight->eta_local) ?
                        '('.$scheduleFlight->eta_local.')' : '' }}</td>
                    <td class="p-1">{{ $scheduleFlight->formatted_ata }} <br> {{ !is_null($scheduleFlight->ata_local) ?
                        '('.$scheduleFlight->ata_local.')' : '' }}</td>
                    <td class="p-1">
                        @if(!is_null($scheduleFlight->arrival_time_diff))
                        @if($scheduleFlight->arrival_time_diff >= 0)
                        <span class="badge bg-success">- {{ $scheduleFlight->formatted_arrival_time_diff }}</span>
                        @else
                        <span class="badge bg-danger">+ {{ $scheduleFlight->formatted_arrival_time_diff }}</span>
                        @endif
                        @endif
                    </td>
                    <td class="p-1">{{ $scheduleFlight->uplifted ?? 0 }}</td>
                    <td class="p-1">{{ $scheduleFlight->flight->aircraftType->formatted_name ?? 'NA' }}</td>
                    <td class="p-1">{{ $scheduleFlight->aircraft->registration ?? 'NA' }}</td>
                    <td class="p-1">{{ $scheduleFlight->utilisation ?? 0 }} %</td>
                    <td class="p-1">{{ $scheduleFlight->offloaded ?? 0 }}</td>
                    <td class="p-1">
                        @if ($scheduleFlight->status === 1)
                        <span class="badge bg-label-info me-1">{{ $scheduleFlight->getStatusLabel() }}</span>
                        @elseif($scheduleFlight->status === 2)
                        <span class="badge bg-label-success me-1">{{ $scheduleFlight->getStatusLabel() }}</span>
                        @elseif($scheduleFlight->status === 3)
                        <span class="badge bg-label-danger me-1">{{ $scheduleFlight->getStatusLabel() }}</span>
                        @elseif($scheduleFlight->status === 4)
                        <span class="badge bg-label-primary me-1">{{ $scheduleFlight->getStatusLabel() }}</span>
                        @endif
                    </td>
                    <td class="p-1">{{ $scheduleFlight->latest_remark ?? 'None' }}</td>
                    <td class="p-1">
                        @can('view schedules')
                        <a href="{{ route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight]) }}"
                            class="text-body">
                            <i class="ti ti-eye ti-sm mx-2 text-primary"></i>
                        </a>
                        @else
                        <span>
                            <i class="ti ti-x ti-sm mx-2 text-secondary"></i>
                        </span>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-info"><strong>There are no flights for this Schedule!</strong></p>
        @endif

        @can('add schedules')
        <div class="mt-3">
            <a href="{{ route('flight-operations.schedule.manual.list', $schedule) }}" class="btn btn-primary me-2">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i> Add Scheduled Flights
            </a>
            <a href="{{ route('flight-operations.schedule.manual.contingency', $schedule) }}" class="btn btn-info mx-2">
                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i> Add contingency Flight
            </a>
        </div>
        @endcan
    </div>
</div>

@endcan

@endsection