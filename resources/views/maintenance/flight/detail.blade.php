@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Flight Detail')

@section('page-script')

<script src="{{asset('assets/js/flight/inactive.js')}}"></script>
<script src="{{asset('assets/js/flight/active.js')}}"></script>
<script src="{{asset('assets/js/flight/delete.js')}}"></script>

<script type="text/javascript">
    const select2 = $('.select2');

    if (select2.length) {
    select2.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
        });
    }).on('change', function() {
        $(this).valid();
    });
    }

</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Flight Detail
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
                Inbound Flight ID: &nbsp;<strong> {{ $processedFlights['inbound']['formatted_id'] ?? 'N/A' }}</strong>
            </span>
            <span class="btn btn-info">
                Outbound Flight ID: &nbsp;<strong> {{ $processedFlights['outbound']['formatted_id'] ?? 'N/A' }}</strong>
            </span>
        </div>
        <a href="{{ route('maintenance.flight.list') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Flight List
        </a>
    </div>
</div>

@can('view flights')

<div class="card mt-3">
    <div class="card-header text-center p-2">
        <span class="btn btn-primary">
            <strong>Inbound Flights</strong>
        </span>
    </div>
    <div class="card-body p-2 align-items-center text-center">
        @if(isset($processedFlights['inbound']) && count($processedFlights['inbound']) > 0 )
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Flight Number</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>STA</th>
                        <th>Aircraft</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Customers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processedFlights['inbound']['days'] as $datum)
                    <tr>
                        <td>{{ Helper::getFlightDaysName($datum->day) }}</td>
                        <td>{{ $processedFlights['inbound']['flight_number'] }}</td>
                        <td>{{ $processedFlights['inbound']['from'] }}</td>
                        <td>{{ $processedFlights['inbound']['to'] }}</td>
                        <td>{{ $processedFlights['inbound']['std'] }}</td>
                        <td>{{ $processedFlights['inbound']['sta'] }}</td>
                        <td>{{ $processedFlights['inbound']['aircraft'] }}</td>
                        <td>{{ $processedFlights['inbound']['capacity'] }}</td>
                        <td>
                            @if ($datum->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @can('view customers')
                            <a href="{{ route('maintenance.flight.day.customer', [$processedFlights['inbound']['id'], $datum]) }}"
                                class="text-body">
                                <i class="ti ti-eye ti-sm mx-2 text-info"></i>
                            </a>
                            @else
                            <span>
                                <i class="ti ti-x ti-sm mx-2 text-secondary"></i>
                            </span>
                            @endcan
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit flights')
                                @if ($datum->active)
                                <a href="{{ route('maintenance.flight.day.inactive', [$processedFlights['inbound']['id'], $datum]) }}"
                                    id="inactive-flight-day-{{$datum->id}}" class="make-inactive-btn">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-danger" title="Make Flight Day Inactive">
                                        <i class="ti ti-power ti-sm mx-2 text-danger"></i>
                                    </span>
                                </a>
                                @else
                                <a href="{{ route('maintenance.flight.day.active', [$processedFlights['inbound']['id'], $datum]) }}"
                                    class="make-active-btn" id="active-flight-day-{{$datum->id}}">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-success" title="Make Flight Day Active">
                                        <i class="ti ti-power ti-sm mx-2 text-success"></i>
                                    </span>
                                </a>
                                @endif
                                @endcan
                                @can('delete flights')
                                <form
                                    action="{{ route('maintenance.flight.day.destroy', [$processedFlights['inbound']['id'], $datum]) }}"
                                    method="post" id="delete-flight-day-form-{{ $datum->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmFlightDayDelete({{ $datum->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-info"><strong>There are no corresponding Inbound Flights</strong></p>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header text-center p-2">
        <span class="btn btn-primary">
            <strong>Outbound Flights</strong>
        </span>
    </div>
    <div class="card-body p-2 align-items-center text-center">
        @if(isset($processedFlights['outbound']) && count($processedFlights['outbound']) > 0 )
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Flight Number</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>STA</th>
                        <th>Aircraft</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Customers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processedFlights['outbound']['days'] as $datum)
                    <tr>
                        <td>{{ Helper::getFlightDaysName($datum->day) }}</td>
                        <td>{{ $processedFlights['outbound']['flight_number'] }}</td>
                        <td>{{ $processedFlights['outbound']['from'] }}</td>
                        <td>{{ $processedFlights['outbound']['to'] }}</td>
                        <td>{{ $processedFlights['outbound']['std'] }}</td>
                        <td>{{ $processedFlights['outbound']['sta'] }}</td>
                        <td>{{ $processedFlights['outbound']['aircraft'] }}</td>
                        <td>{{ $processedFlights['outbound']['capacity'] }}</td>
                        <td>
                            @if ($datum->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @can('view customers')
                            <a href="{{ route('maintenance.flight.day.customer', [$processedFlights['outbound']['id'], $datum]) }}"
                                class="text-body">
                                <i class="ti ti-eye ti-sm mx-2 text-info"></i>
                            </a>
                            @else
                            <span>
                                <i class="ti ti-x ti-sm mx-2 text-secondary"></i>
                            </span>
                            @endcan
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit flights')
                                @if ($datum->active)
                                <a href="{{ route('maintenance.flight.day.inactive', [$processedFlights['outbound']['id'], $datum]) }}"
                                    id="inactive-flight-day-{{$datum->id}}" class="make-inactive-btn">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-danger" title="Make Flight Day Inactive">
                                        <i class="ti ti-power ti-sm mx-2 text-danger"></i>
                                    </span>
                                </a>
                                @else
                                <a href="{{ route('maintenance.flight.day.active', [$processedFlights['outbound']['id'], $datum]) }}"
                                    class="make-active-btn" id="active-flight-day-{{$datum->id}}">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-success" title="Make Flight Day Active">
                                        <i class="ti ti-power ti-sm mx-2 text-success"></i>
                                    </span>
                                </a>
                                @endif
                                @endcan
                                @can('delete flights')
                                <form
                                    action="{{ route('maintenance.flight.day.destroy', [$processedFlights['outbound']['id'], $datum]) }}"
                                    method="post" id="delete-flight-day-form-{{ $datum->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmFlightDayDelete({{ $datum->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-info"><strong>There are no corresponding Outbound Flights</strong></p>
        @endif
    </div>
</div>

@endcan

{{--
<!-- Edit Modal -->
<div class="modal" id="editCustomerEmail" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Customer Email</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal --> --}}

@endsection