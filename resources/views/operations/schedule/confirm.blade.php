@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Confirm Schedule')

@section('page-script')

@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Schedules
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
    <div class="card-header text-center">
        <h5>Confirm New Schedule</h5>
    </div>
    <div class="card-body text-center">
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</p>

        <p><strong>Description:</strong> {{ $request->description }}</p>

        <p><strong>Schedule Type:</strong> {{ ucfirst($request->schedule_type) }}</p>

        @if(count($flights) > 0)
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Flight Number</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>STA</th>
                        <th>Aircraft Type</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($flights as $flight)
                    <tr>
                        <td>{{ $flight->flight_number }}</td>
                        <td>{{ $flight->fromAirport->iata ?? '' }}</td>
                        <td>{{ $flight->toAirport->iata ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($flight->departure_time)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($flight->arrival_time)->format('H:i') }}</td>
                        <td>{{ $flight->aircraftType->formatted_name ?? '' }}</td>
                        <td>{{ $flight->aircraftType->capacity ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p>
            No flights have been added to the schedule.

            You can use the Flight Summary page to manually add flights to the Schedule.
        </p>
        @endif

        <form id="confirmScheduleForm" class="row g-3 mt-2" method="POST"
            action="{{ route('flight-operations.schedule.store') }}">
            @csrf

            <input type="hidden" name="request" value="{{ json_encode(request()->all()) }}">
            <input type="hidden" name="flights"
                value="{{ $flights->isNotEmpty() ? $flights->pluck('id')->implode(',') : '' }}">

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-2">Confirm</button>
                <a href="{{ route('flight-operations.schedule.list') }}" class="btn btn-danger me-2">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</div>

@endsection