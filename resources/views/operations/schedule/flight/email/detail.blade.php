@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Schedule Flight Update Email')

@section('page-script')

<script src="{{asset('assets/js/schedule/flight/email/send.js')}}"></script>

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

    $('.preview-email-btn').on('click', function () {
        let scheduleFlightCustomerId = $(this).data('record-id');
        let isRemarkIncluded = $('#includeRemark' + scheduleFlightCustomerId).prop('checked');

        // Load the preview email form content into the modal
        $.ajax({
            url: '{{ route("flight-operations.schedule.flight.email.preview", [":scheduleId", ":scheduleFlightId", ":recordId"]) }}'.replace(':scheduleId', {{ $schedule->id }}).replace(':scheduleFlightId', {{ $scheduleFlight->id }}).replace(':recordId', scheduleFlightCustomerId),
            type: 'GET',
            data: { isRemarkIncluded },
            success: function (response) {
                $('#previewScheduleFlightEmail .modal-form').html(response);
                $('#previewScheduleFlightEmail').show();
            }
        });
    });

</script>

@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Schedule Flight Email
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
    <div class="card-header d-flex justify-content-between">
        <h5>Send Flight Update Emails</h5>
        <div>
            @can('view schedules')
            <a href="{{ route('flight-operations.schedule.flight.show', [$schedule, $scheduleFlight]) }}"
                class="btn btn-secondary">
                <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Flight Detail
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
                        <th>From Airport</th>
                        <td>{{ $scheduleFlight->flight->fromAirport->iata ?? '' }}</td>
                        <th>To Airport</th>
                        <td>{{ $scheduleFlight->flight->toAirport->iata ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>STD</th>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->departure_time)->format('H:i') }}</td>
                        <th>STA</th>
                        <td>{{ \Carbon\Carbon::parse($scheduleFlight->flight->arrival_time)->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th>ETD</th>
                        <td>{{ $scheduleFlight->formatted_etd }}</td>
                        <th>ETA</th>
                        <td>{{ $scheduleFlight->formatted_eta }}</td>
                    </tr>
                    <tr>
                        <th>ATD</th>
                        <td>{{ $scheduleFlight->formatted_atd }}</td>
                        <th>ATA</th>
                        <td>{{ $scheduleFlight->formatted_ata }}</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Email Details</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('add schedules')
            <a href="{{ route('flight-operations.schedule.flight.email.send', [$schedule, $scheduleFlight]) }}"
                class="btn btn-primary send-email-btn">
                <i class="ti ti-mail me-0 me-sm-1 ti-xs"></i> Send Emails
            </a>
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
                        <th>Include Remark</th>
                        <th>Send Email</th>
                        <th>Actions</th>
                        <th>Emails Sent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleFlight->scheduleFlightCustomers as $datum)
                    <tr>
                        <td>{{ $datum->customer->name }}</td>
                        <td>
                            <input type="checkbox" name="include_remark[]" id="includeRemark{{$datum->id}}"
                                value="{{$datum->id}}" checked>
                        </td>
                        <td>
                            <input type="checkbox" name="send_email[]" id="sendEmail{{$datum->id}}"
                                value="{{$datum->id}}" checked>
                        </td>
                        <td>
                            @if($scheduleFlight->status === 1)
                            <div class="d-flex align-items-center justify-content-center">
                                @can('view schedules')
                                <a href="javascript:;" class="text-body preview-email-btn"
                                    data-record-id="{{ $datum->id }}" data-bs-toggle="modal"
                                    data-bs-target="#previewScheduleFlightEmail"><i
                                        class="ti ti-mail ti-sm mx-2 text-info"></i></a>
                                @endcan
                            </div>
                            @else
                            <span><i class="ti ti-x ti-sm text-danger"></i></span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-label-success">
                                {{ $scheduleFlight->scheduleFlightEmails->where('customer_id',
                                $datum->customer_id)->count() ?? 0 }}
                            </span>
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

<!-- Preview Modal -->
<div class="modal" id="previewScheduleFlightEmail" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Preview Email</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Preview Modal -->

@endsection