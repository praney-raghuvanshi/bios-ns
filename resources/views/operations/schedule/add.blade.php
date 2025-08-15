@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Create Schedule')

@section('page-script')

<script src="{{asset('assets/js/schedule/add.js')}}"></script>

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
        <h5>Add New Schedule</h5>
    </div>
    <div class="card-body d-flex justify-content-center align-items-center">
        <!-- Add Schedule  -->
        <form id="addScheduleForm" class="row g-3" method="POST"
            action="{{ route('flight-operations.schedule.confirm') }}">
            @csrf
            <div class="col-12">
                <label class="form-label" for="scheduleDate">Schedule Date</label>
                <input type="date" name="date" class="form-control ddmmyyyyDateFormat"
                    value="{{ old('date', date('Y-m-d')) }}" />
            </div>

            <div class="col-12">
                <label class="form-label" for="description">Description</label>
                <input type="text" name="description" class="form-control"
                    value="{{ old('description', 'Schedule for ' . date('d/m/Y')) }}" />
            </div>

            <!-- New Radio Button Group -->
            <div class="col-12">
                <label class="form-label">How would you like to build the new Schedule :</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="schedule_type" value="manual" id="manual"
                        @if(old('schedule_type')==='manual' ) checked @endif>
                    <label class="form-check-label" for="manual">Build the schedule manually by entering the flights
                        individually</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="schedule_type" value="auto" id="auto"
                        @if(old('schedule_type', 'auto' )==='auto' ) checked @endif>
                    <label class="form-check-label" for="auto">Build the schedule automatically from the schedule master
                        (<strong>default option</strong>)</label>
                </div>
                {{-- <div class="form-check">
                    <input class="form-check-input" type="radio" name="schedule_type" value="previous" id="previous"
                        @if(old('schedule_type')==='previous' ) checked @endif>
                    <label class="form-check-label" for="previous">Copy the schedule from a previous schedule on the
                        same week day</label>
                </div> --}}
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary me-2">Submit</button>
            </div>
        </form>
        <!--/ Add Schedule -->
    </div>
</div>

@endsection