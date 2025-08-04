@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Add contingency Flight to Schedule')

@section('page-script')

<script src="{{asset('assets/js/schedule/manual/contingency/add.js')}}"></script>

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
        <h5>Add contingency Flight</h5>
    </div>
    <div class="card-body d-flex justify-content-center align-items-center">
        <!-- Add contingency Flight  -->
        <form id="addcontingencyFlightForScheduleForm" class="row g-3" method="POST"
            action="{{ route('flight-operations.schedule.manual.contingency.store', $schedule) }}">
            @csrf

            <div class="col-12 text-center">
                <label class="form-label" for="day">Day</label>
                <p><strong>{{ Helper::getFlightDaysName($schedule->day) }}</strong></p>
            </div>

            <div class="col-12 text-center">
                <div id="flightMessage" class="text-danger"></div>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="flight">Flight Number</label>
                <div class="d-flex">
                    <input type="text" name="flight" id="flight" class="form-control me-2" value="{{ old('flight') }}">
                    <button type="button" class="btn btn-info check-flight">Lookup</button>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Direction</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="direction" value="inbound" id="inbound"
                        @if(old('direction')==='inbound' ) checked @endif disabled>
                    <label class="form-check-label" for="direction">Inbound</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="direction" value="outbound" id="outbound"
                        @if(old('direction')==='outbound' ) checked @endif disabled>
                    <label class="form-check-label" for="direction">Outbound</label>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Location</label>
                <select class="form-select select2" name="location" id="location" disabled>
                    <option value="">-- Select Location --</option>
                    @foreach ($locations as $location)
                    <option value="{{ $location->id }}" @if(old('location' )==$location->id) selected @endif
                        >{{ $location->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Arrival Day</label>
                <input type="number" name="arrival_day" min="0" class="form-control" value="{{ old('arrival_day', 0) }}"
                    id="arrivalDay" disabled>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">From</label>
                <select class="form-select select2" name="from" id="from" disabled>
                    <option value="">-- Select Airport --</option>
                    @foreach ($airports as $airport)
                    <option value="{{ $airport->id }}" @if(old('from' )==$airport->id) selected @endif
                        >{{ $airport->iata }} : {{ $airport->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">To</label>
                <select class="form-select select2" name="to" id="to" disabled>
                    <option value="">-- Select Airport --</option>
                    @foreach ($airports as $airport)
                    <option value="{{ $airport->id }}" @if(old('to' )==$airport->id) selected @endif
                        >{{ $airport->iata }} : {{ $airport->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">STD</label>
                <input type="time" name="departure_time" class="form-control time24" value="{{ old('departure_time') }}"
                    id="std" disabled>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">STA</label>
                <input type="time" name="arrival_time" class="form-control time24" value="{{ old('arrival_time') }}"
                    id="sta" disabled>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Aircraft</label>
                <select class="form-select select2" name="aircraft" id="aircraft" disabled>
                    <option value="">-- Select Aircraft --</option>
                    @foreach ($aircraftTypes as $aircraftType)
                    <option value="{{ $aircraftType->id }}" @if(old('aircraft')==$aircraftType->id) selected
                        @endif >{{ $aircraftType->name }} ({{ $aircraftType->capacity ?? 0
                        }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Effective Date</label>
                <input type="date" name="effective_date" class="form-control"
                    value="{{ old('effective_date', date('Y-m-d')) }}" id="effectiveDate" disabled>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-2">Submit</button>
            </div>
        </form>
        <!--/ Add contingency Flight -->
    </div>
</div>

@endsection