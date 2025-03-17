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
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Flight</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>ETD</th>
                        <th>ATD</th>
                        <th>+/- Min</th>
                        <th>STA</th>
                        <th>ETA</th>
                        <th>ATA</th>
                        <th>+/- Min</th>
                        <th>Uplifted</th>
                        <th>Utilisation</th>
                        <th>Off Loaded</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedule->scheduleFlights as $scheduleFlight)
                    <tr>
                        <td>{{ $scheduleFlight->flight->flight_number }}</td>
                        <td>{{ $scheduleFlight->flight->fromAirport->iata ?? '' }}</td>
                        <td>{{ $scheduleFlight->flight->toAirport->iata ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i') }}</td>
                        <td>{{ $scheduleFlight->formatted_etd }}</td>
                        <td>{{ $scheduleFlight->formatted_atd }}</td>
                        <td>
                            @if(!is_null($scheduleFlight->departure_time_diff))
                            @if($scheduleFlight->departure_time_diff >= 0)
                            <span class="badge bg-success">- {{ $scheduleFlight->formatted_departure_time_diff }}</span>
                            @else
                            <span class="badge bg-danger">+ {{ $scheduleFlight->formatted_departure_time_diff }}</span>
                            @endif
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i') }}</td>
                        <td>{{ $scheduleFlight->formatted_eta }}</td>
                        <td>{{ $scheduleFlight->formatted_ata }}</td>
                        <td>
                            @if(!is_null($scheduleFlight->arrival_time_diff))
                            @if($scheduleFlight->arrival_time_diff >= 0)
                            <span class="badge bg-success">- {{ $scheduleFlight->formatted_arrival_time_diff }}</span>
                            @else
                            <span class="badge bg-danger">+ {{ $scheduleFlight->formatted_arrival_time_diff }}</span>
                            @endif
                            @endif
                        </td>
                        <td>{{ $scheduleFlight->uplifted ?? 0 }}</td>
                        <td>{{ $scheduleFlight->utilisation ?? 0 }} %</td>
                        <td>{{ $scheduleFlight->offloaded ?? 0 }}</td>
                        <td>
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
                        <td>{{ $scheduleFlight->latest_remark ?? 'None' }}</td>
                        <td>
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
        </div>
        @else
        <p class="text-info"><strong>There are no flights for this Schedule!</strong></p>
        @endif
    </div>
</div>

@endcan

@endsection