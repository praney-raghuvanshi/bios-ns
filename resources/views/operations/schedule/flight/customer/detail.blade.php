@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Schedule Flight Customer Detail')

@section('page-script')
<script src="{{asset('assets/js/schedule/flight/customer/product/delete.js')}}"></script>
<script src="{{asset('assets/js/schedule/flight/customer/shipment/add.js')}}"></script>
<script src="{{asset('assets/js/schedule/flight/customer/shipment/delete.js')}}"></script>

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
        let scheduleFlightCustomerProductId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("flight-operations.schedule.flight.customer.product.edit", [":scheduleId", ":scheduleFlightId", ":scheduleFlightCustomerId", ":recordId"]) }}'.replace(':scheduleId', {{ $schedule->id }}).replace(':scheduleFlightId', {{ $scheduleFlight->id }}).replace(':scheduleFlightCustomerId', {{ $scheduleFlightCustomer->id }}).replace(':recordId', scheduleFlightCustomerProductId),
            type: 'GET',
            success: function (response) {
                $('#editScheduleFlightCustomerProduct .modal-form').html(response);
                $('#editScheduleFlightCustomerProduct').show();

                // Initialize jQuery Validation
                $('#editScheduleFlightCustomerProductForm').validate({
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
                        uplifted_weight: {
                            required: true
                        }
                    }
                });
            }
        });
    });

    $('.edit-awb-btn').on('click', function () {
        let scheduleFlightCustomerAwbId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("flight-operations.schedule.flight.customer.shipment.edit", [":scheduleId", ":scheduleFlightId", ":scheduleFlightCustomerId", ":recordId"]) }}'.replace(':scheduleId', {{ $schedule->id }}).replace(':scheduleFlightId', {{ $scheduleFlight->id }}).replace(':scheduleFlightCustomerId', {{ $scheduleFlightCustomer->id }}).replace(':recordId', scheduleFlightCustomerAwbId),
            type: 'GET',
            success: function (response) {
                $('#editScheduleFlightCustomerAwb .modal-form').html(response);
                $('#editScheduleFlightCustomerAwb').show();

                // Initialize jQuery Validation
                $('#editScheduleFlightCustomerAwbForm').validate({
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
                        declared_weight: {
                            required: true
                        },
                        actual_weight: {
                            required: true
                        },
                        total_actual_weight: {
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
    <span class="text-muted fw-light">BIOS /</span> Schedule Flight Customer
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
        <h5>Schedule Flight Customer Details</h5>
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
            <table class="table table-bordered text-center mt-2">
                <tr>
                    <th>Customer</th>
                    <td>{{ $scheduleFlightCustomer->customer->name }}</td>
                </tr>
                <tr>
                    <th>Uplifted Weight</th>
                    <td>{{ $scheduleFlightCustomer->total_uplifted_weight }}</td>
                </tr>
                <tr>
                    <th>Offloaded Weight</th>
                    <td>{{ $scheduleFlightCustomer->total_offloaded_weight }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Product Details</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('edit schedules')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#addScheduleFlightCustomerProduct">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add Product</span>
            </button>
            @endcan
            @endif
        </div>
    </div>
    <div class="card-body align-items-center text-center">
        @if($scheduleFlightCustomer->scheduleFlightCustomerProducts->isNotEmpty())
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Uplifted Weight</th>
                        <th>Offloaded Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleFlightCustomer->scheduleFlightCustomerProducts as $datum)
                    <tr>
                        <td>{{ $datum->product->name }}</td>
                        <td>{{ $datum->uplifted_weight }}</td>
                        <td>{{ $datum->offloaded_weight }}</td>
                        <td>
                            @if($scheduleFlight->status === 1)
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit schedules')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $datum->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editScheduleFlightCustomerProduct"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete schedules')
                                <form
                                    action="{{ route('flight-operations.schedule.flight.customer.product.destroy', [$schedule, $scheduleFlight, $scheduleFlightCustomer, $datum]) }}"
                                    method="post" id="delete-schedule-flight-customer-product-form-{{ $datum->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmScheduleFlightCustomerProductDelete({{ $datum->id }})">
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
        <p class="text-info"><strong>There are no products for this customer!</strong></p>
        @endif
    </div>
</div>

@if($scheduleFlightCustomer->scheduleFlightCustomerProducts->isNotEmpty())
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>AWB Details</h5>
        <div>
            @if($scheduleFlight->status === 1)
            @can('edit schedules')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#addScheduleFlightCustomerAwb">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add AWB</span>
            </button>
            @endcan
            @endif
        </div>
    </div>
    <div class="card-body align-items-center text-center">
        @if($scheduleFlightCustomer->scheduleFlightCustomerShipments->isNotEmpty())

        <div>
            @foreach ($scheduleFlightCustomer->productAndAwbData() as $item)
            @if($item['is_equal'])
            <span class="badge bg-success">{{ $item['msg'] }}</span>
            @else
            <span class="badge bg-danger">{{ $item['msg'] }}</span>
            @endif
            @endforeach
        </div>

        <div class="table-responsive text-nowrap mt-2">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>AWB</th>
                        <th>Product</th>
                        <th>Declared Weight</th>
                        <th>Actual Weight</th>
                        <th>Volume Weight</th>
                        <th>Uplifted</th>
                        <th>Offloaded</th>
                        <th>Destination</th>
                        <th>Type</th>
                        <th>Total Actual Weight</th>
                        <th>Total Volume Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleFlightCustomer->scheduleFlightCustomerShipments as $datum)
                    <tr>
                        <td>{{ $datum->awb }}</td>
                        <td>{{ $datum->product->name }}</td>
                        <td>{{ $datum->declared_weight }}</td>
                        <td>{{ $datum->actual_weight }}</td>
                        <td>{{ $datum->volumetric_weight }}</td>
                        <td>{{ $datum->uplifted_weight }}</td>
                        <td>{{ $datum->offloaded_weight }}</td>
                        <td>{{ $datum->toAirport->iata }}</td>
                        <td>{{ $datum->type }}</td>
                        <td>{{ $datum->total_actual_weight }}</td>
                        <td>{{ $datum->total_volumetric_weight }}</td>
                        <td>
                            @if($scheduleFlight->status === 1)
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit schedules')
                                <a href="javascript:;" class="text-body edit-awb-btn" data-record-id="{{ $datum->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editScheduleFlightCustomerAwb"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete schedules')
                                <form
                                    action="{{ route('flight-operations.schedule.flight.customer.shipment.destroy', [$schedule, $scheduleFlight, $scheduleFlightCustomer, $datum]) }}"
                                    method="post" id="delete-schedule-flight-customer-awb-form-{{ $datum->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmScheduleFlightCustomerAwbDelete({{ $datum->id }})">
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
        <p class="text-info"><strong>There are no AWB's for this customer!</strong></p>
        @endif
    </div>
</div>
@endif

@include('_partials/_modals/schedule/flight/customer/product/add')
@include('_partials/_modals/schedule/flight/customer/shipment/add')

<!-- Edit Modal -->
<div class="modal" id="editScheduleFlightCustomerProduct" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Product</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

<!-- Edit AWB Modal -->
<div class="modal" id="editScheduleFlightCustomerAwb" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit AWB</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit AWB Modal -->

@endsection