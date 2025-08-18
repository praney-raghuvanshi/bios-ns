@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Schedule Flight Detail')

@section('page-script')

<script src="{{asset('assets/js/schedule/flight/remark/delete.js')}}"></script>
<script src="{{asset('assets/js/schedule/flight/actions.js')}}"></script>

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

    $('.edit-btn').on('click', function () {
        let scheduleFlightRemarkId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("flight-operations.schedule.flight.remark.edit", [":scheduleId", ":scheduleFlightId", ":recordId"]) }}'.replace(':scheduleId', {{ $schedule->id }}).replace(':scheduleFlightId', {{ $scheduleFlight->id }}).replace(':recordId', scheduleFlightRemarkId),
            type: 'GET',
            success: function (response) {
                $('#editScheduleFlightRemark .modal-form').html(response);
                $('#editScheduleFlightRemark').show();

                const select2ForEdit = $('.select2ForEdit');

                if (select2ForEdit.length) {
                    select2ForEdit.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                    });
                }).on('change', function() {
                    $(this).valid();
                });
                }

                // Initialize jQuery Validation
                $('#editScheduleFlightRemarkForm').validate({
                    errorElement: 'div',
                    errorClass: 'invalid-feedback',
                    highlight: function (element, errorClass, validClass) {
                        $(element).addClass('is-invalid').removeClass('is-valid');
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function (error, element) {
                        error.appendTo(element.parent().append('<div class="form-control-feedback"></div>'));
                    },
                    rules: {
                        remark: {
                            required: true
                        }
                    }
                });
            }
        });
    });

</script>

@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Schedule Flight
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

@if($scheduleFlight->status === 3)
<div class="alert alert-danger text-center" role="alert">
    <h4 class="text-danger">Flight has been Cancelled!</h4>
</div>
@endif


<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Schedule Flight Details</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('edit schedules')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editScheduleFlight">
                <span><i class="ti ti-edit me-0 me-sm-1 ti-xs"></i>Edit Details</span>
            </button>
            <a href="{{ route('flight-operations.schedule.flight.mark-complete', [$schedule, $scheduleFlight]) }}"
                class="btn btn-success mark-complete">
                <span><i class="ti ti-check me-0 me-sm-1 ti-xs"></i>Mark Complete</span>
            </a>
            @endcan
            @can('add schedules')
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addScheduleFlightRemark">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add Remarks</span>
            </button>
            @endcan
            @can('delete schedules')
            {{-- <a href="{{ route('flight-operations.schedule.show', $schedule) }}"
                class="btn btn-danger delete-flight">
                <span><i class="ti ti-trash me-0 me-sm-1 ti-xs"></i>Delete Flight</span>
            </a> --}}
            <a href="{{ route('flight-operations.schedule.flight.cancel', [$schedule, $scheduleFlight]) }}"
                class="btn btn-danger cancel-flight">
                <span><i class="ti ti-x me-0 me-sm-1 ti-xs"></i>Cancel Flight</span>
            </a>
            @endcan
            @elseif($scheduleFlight->status === 2)
            @can('edit schedules')
            <a href="{{ route('flight-operations.schedule.flight.re-open', [$schedule, $scheduleFlight]) }}"
                class="btn btn-success re-open">
                <span><i class="ti ti-arrow-back-up me-0 me-sm-1 ti-xs"></i>Re-Open</span>
            </a>
            @endcan
            @endif
            @can('view schedules')
            <a href="{{ route('flight-operations.schedule.show', $schedule) }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Flight List
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body align-items-center text-center">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Schedule Date</th>
                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('l d F Y') }}</td>
                        <th>Flight</th>
                        <td>{{ $scheduleFlight->flight->flight_number }}</td>
                    </tr>
                    <tr>
                        <th>Aircraft Type</th>
                        <td>{{ $scheduleFlight->flight->aircraftType->formatted_name ?? 'NA' }}</td>
                        <th>Aircraft Registration</th>
                        <td>{{ $scheduleFlight->aircraft->registration ?? 'NA' }}</td>
                    </tr>
                    <tr>
                        <th>From Airport</th>
                        <td>{{ $scheduleFlight->flight->fromAirport->iata ?? '' }}</td>
                        <th>To Airport</th>
                        <td>{{ $scheduleFlight->flight->toAirport->iata ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>STD</th>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i') }} ({{
                            $scheduleFlight->flight->departure_time_local }})</td>
                        <th>STA</th>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i') }} ({{
                            $scheduleFlight->flight->arrival_time_local }})</td>
                    </tr>
                    <tr>
                        <th>ETD</th>
                        <td>{{ $scheduleFlight->formatted_etd }} {{ !is_null($scheduleFlight->etd_local) ?
                            '('.$scheduleFlight->etd_local.')' : '' }}</td>
                        <th>ETA</th>
                        <td>{{ $scheduleFlight->formatted_eta }} {{ !is_null($scheduleFlight->eta_local) ?
                            '('.$scheduleFlight->eta_local.')' : '' }}</td>
                    </tr>
                    <tr>
                        <th>ATD</th>
                        <td>{{ $scheduleFlight->formatted_atd }} {{ !is_null($scheduleFlight->atd_local) ?
                            '('.$scheduleFlight->atd_local.')' : '' }}</td>
                        <th>ATA</th>
                        <td>{{ $scheduleFlight->formatted_ata }} {{ !is_null($scheduleFlight->ata_local) ?
                            '('.$scheduleFlight->ata_local.')' : '' }}</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Customer Details</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('edit schedules')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#addScheduleFlightCustomer">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add Customer</span>
            </button>
            @endcan
            @endif
        </div>
    </div>
    <div class="card-body align-items-center text-center">
        @if($scheduleFlight->scheduleFlightCustomers->isNotEmpty())
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Uplifted Weight</th>
                        <th>Offloaded Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleFlight->scheduleFlightCustomers as $datum)
                    <tr>
                        <td>{{ $datum->customer->name }}</td>
                        <td>{{ $datum->total_uplifted_weight }}</td>
                        <td>{{ $datum->total_offloaded_weight }}</td>
                        <td>
                            @can('view schedules')
                            <a href="{{ route('flight-operations.schedule.flight.customer.show', [$schedule, $scheduleFlight, $datum]) }}"
                                class="text-body">
                                <i class="ti ti-eye ti-sm mx-2 text-primary"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-info"><strong>There are no customers for this flight!</strong></p>
        @endif
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Internal Flight Remarks (DFR)</h5>
    </div>
    <div class="card-body align-items-center text-center">
        @if(count($dfrRemarks) > 0)
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Remark Datetime</th>
                        <th>User</th>
                        <th>Remark</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dfrRemarks as $remark)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($remark->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $remark->addedByUser->name }}</td>
                        <td>{{ $remark->remark }}</td>
                        <td>
                            @if($scheduleFlight->status === 1)
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit schedules')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $remark->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editScheduleFlightRemark"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete schedules')
                                <form
                                    action="{{ route('flight-operations.schedule.flight.remark.destroy', [$schedule, $scheduleFlight, $remark]) }}"
                                    method="post" id="delete-schedule-flight-remark-form-{{ $remark->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmScheduleFlightRemarkDelete({{ $remark->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                            @else
                            <span><i class="ti ti-x ti-sm text-danger"></i></span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-info"><strong>There are no Internal Flight Remarks for this flight!</strong></p>
        @endif
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Customer Flight Performance Remarks (FPR)</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('add schedules')
            <a href="{{ route('flight-operations.schedule.flight.email', [$schedule, $scheduleFlight]) }}"
                class="btn btn-primary">
                <i class="ti ti-mail ti-sm mx-2"></i> Send Emails
            </a>
            @endcan
            @endif
        </div>
    </div>
    <div class="card-body align-items-center text-center">
        @if(count($fprRemarks) > 0)
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Remark Datetime</th>
                        <th>User</th>
                        <th>Customer</th>
                        <th>Remark</th>
                        <th>Email</th>
                        <th>FPR</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fprRemarks as $remark)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($remark->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $remark->addedByUser->name }}</td>
                        <td>{{ $remark->customer->name }}</td>
                        <td>{{ $remark->remark }}</td>
                        <td>
                            @if ($remark->email_required)
                            <span class="badge bg-label-success me-1">Yes</span>
                            @else
                            <span class="badge bg-label-danger me-1">No</span>
                            @endif
                        </td>
                        <td>
                            @if ($remark->is_fpr)
                            <span class="badge bg-label-success me-1">Yes</span>
                            @else
                            <span class="badge bg-label-danger me-1">No</span>
                            @endif
                        </td>
                        <td>
                            @if($scheduleFlight->status === 1)
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit schedules')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $remark->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editScheduleFlightRemark"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete schedules')
                                <form
                                    action="{{ route('flight-operations.schedule.flight.remark.destroy', [$schedule, $scheduleFlight, $remark]) }}"
                                    method="post" id="delete-schedule-flight-remark-form-{{ $remark->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmScheduleFlightRemarkDelete({{ $remark->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                            @else
                            <span><i class="ti ti-x ti-sm text-danger"></i></span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-info"><strong>There are no Flight Performance Remarks for this flight!</strong></p>
        @endif
    </div>
</div>

@can('view schedules')
<div class="card shadow-sm rounded border-0 mt-2">
    <div class="card-header bg-label-primary d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">📜 Audit Trail</h5>
    </div>

    <div class="card-body p-0">
        @if($logs->isEmpty())
        <div class="p-3 text-center text-muted">No audit logs available.</div>
        @else
        <ul class="list-group list-group-flush">
            @foreach($logs as $log)
            @if($log->description)
            <li class="list-group-item py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info"
                            title="{{ $log->description }}">
                            {{ \Illuminate\Support\Str::limit($log->description, 130) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-primary">
                            <i class="ti ti-user me-1"></i>
                            {{ optional($log->user)->name ?? 'System' }}</span>
                        <small class="text-muted">
                            <i class="ti ti-calendar-time mx-2 me-1"></i>
                            {{ \Carbon\Carbon::parse($log->performed_at)->format('d M Y, H:i') }}
                        </small>
                    </div>
                </div>
            </li>
            @endif
            @endforeach
        </ul>
        @endif
    </div>
</div>
@endcan

@include('_partials/_modals/schedule/flight/edit')

@include('_partials/_modals/schedule/flight/customer/add')

@include('_partials/_modals/schedule/flight/remark/add')

<!-- Edit Modal -->
<div class="modal" id="editScheduleFlightRemark" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Remark</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection