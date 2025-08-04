@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Add Manual Flights to Schedule')

@section('page-script')
<script src="{{asset('assets/js/schedule/manual/add.js')}}"></script>
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
        <h5>Add Flights</h5>
    </div>
    <div class="card-body text-center">
        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</p>

        <p><strong>Description:</strong> {{ $schedule->description }}</p>

        <p><strong>Schedule Type:</strong> {{ ucfirst($schedule->schedule_type) }}</p>

        @if(count($flights) > 0)
        <form id="confirmFlightsForScheduleForm" class="row g-3 mt-2" method="POST"
            action="{{ route('flight-operations.schedule.manual.store', $schedule) }}">
            @csrf
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Flight Number</th>
                            <th>From</th>
                            <th>To</th>
                            <th>STD</th>
                            <th>STA</th>
                            <th>Aircraft</th>
                            <th>Capacity</th>
                            <th></th>
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
                            <td>{{ $flight->aircraft->capacity ?? '' }}</td>
                            <td>
                                <input type="checkbox" name="flight[]" value="{{$flight->id}}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-2">Confirm</button>
                <a href="{{ route('flight-operations.schedule.list') }}" class="btn btn-danger me-2">
                    Cancel
                </a>
            </div>
        </form>
        @else
        <p>
            No flights available.
        </p>
        @endif

    </div>
</div>

@endsection