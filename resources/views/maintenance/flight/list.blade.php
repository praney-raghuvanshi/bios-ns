@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Flight List')

@section('page-script')

<script src="{{asset('assets/js/flight/add.js')}}"></script>
<script src="{{asset('assets/js/flight/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
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

    // Setup - add a text input to each header cell
    $('#flights-table thead tr').clone(true).appendTo('#flights-table thead');

    var dt = $('#flights-table').DataTable({
        orderCellsTop: true,
        stateSave: true,
        order: [],
        dom:    '<"row align-items-center mb-2"' +
                    '<"col-sm-12 col-md-6 d-flex align-items-center" l>' +
                    '<"col-sm-12 col-md-6 d-flex justify-content-md-end gap-2"B f>' +
                '>' +
                '<"table-responsive table-bordered" t>' +
                '<"row mt-2"' +
                    '<"col-sm-12 col-md-6" i>' +
                    '<"col-sm-12 col-md-6 d-flex justify-content-md-end" p>' +
                '>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-info'            
            },
            {
                text: '<i class="fas fa-filter"></i> Reset',
                className: 'btn btn-secondary',
                action: function (e, dt, node, config) {
                    dt.search('').columns().search('').draw();
                    $('#flights-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                targets: [0, 8, 18], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#flights-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        if (![0, 8, 18].includes(i)) { // Exclude the "Actions" column from searching
          $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
          $(this).html('<input type="text" class="form-control" disabled />');
        }

        $('input', this).on('keyup change', function () {
            if (![0, 8, 18].includes(i) && dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });

    $('#location, #include_inactive').change(function () {
        document.getElementById("listFlightForm").submit(); // Submit form on change
    });

    $("input[name='direction']").change(function () {
        let value = $(this).val();
        
        $("#inboundForm input, #inboundForm select").prop("disabled", value === 'outbound');
        $("#outboundForm input, #outboundForm select").prop("disabled", value === 'inbound');
    });


    let selectedDirectionValue = $("input[name='direction']:checked").val();
    if (selectedDirectionValue) {
        $("input[name='direction']:checked").trigger('change');
    }

    $('.edit-btn').on('click', function () {
        let flightId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("maintenance.flight.edit", ":flightId") }}'.replace(':flightId', flightId),
            type: 'GET',
            success: function (response) {
                $('#editFlight .modal-form').html(response);
                $('#editFlight').show();

                // Initialize jQuery Validation
                $('#editFlightForm').validate({
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
                        code: {
                            required: true
                        },
                        name: {
                            required: true
                        },
                        status: {
                            required: true
                        }
                    }
                });
            }
        });
    });
});
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Flights
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
    @if(is_array(session('failure')))
    @foreach(session('failure') as $error)
    <p>{{ $error }}</p> {{-- Display each error as a paragraph --}}
    @endforeach
    @else
    {{ session('failure') }} {{-- Display if it's a string --}}
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('warning'))
<div class="alert alert-warning alert-dismissible" role="alert">
    @if(is_array(session('warning')))
    @foreach(session('warning') as $warning)
    <p>{{ $warning }}</p> {{-- Display each warning as a paragraph --}}
    @endforeach
    @else
    {{ session('warning') }} {{-- Display if it's a string --}}
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

<div class="card mt-2">
    <div class="card-body">
        <form id="listFlightForm" method="GET" action="{{ route('maintenance.flight.list') }}">

            <div class="row mb-3 justify-content-center align-items-center">
                <div class="col-md-3 text-end">
                    <label for="location" class="form-label fw-bold">Location</label>
                </div>
                <div class="col-md-4 d-flex">
                    <select class="form-select select2 w-100 mx-auto" name="location" id="location">
                        <option value="" selected>-- Select Location --</option>
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @if(old('location', session('location', request()->
                            location))==$location->id)
                            selected @endif>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                    <span id="location-error" class="error p-2"></span>
                </div>
            </div>

            <!-- Second Row: Checkbox & Legend Centered -->
            <div class="row align-items-center justify-content-center">
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <input class="form-check-input me-2" type="checkbox" id="include_inactive" name="include_inactive"
                        {{ old('include_inactive', request()->include_inactive) ? 'checked' : '' }}>
                    <label class="form-check-label me-3" for="include_inactive">Show Inactive Flights</label>

                    <!-- Legend -->
                    <span class="badge bg-success me-2">Active</span>
                    <span class="badge bg-danger">Inactive</span>
                </div>
            </div>

        </form>
    </div>
</div>

@if(count($processedFlights) > 0)
<!-- Bordered Table -->
<div class="card mt-2">
    <div class="card-header d-flex justify-content-between">
        <h5>Flights</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="flights-table">
                <thead>
                    <tr>
                        <th>Days</th>
                        <th>Flight Number</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>STA</th>
                        <th>Aircraft</th>
                        <th>Load</th>
                        <th></th>
                        <th>Flight Number</th>
                        <th>From</th>
                        <th>To</th>
                        <th>STD</th>
                        <th>STA</th>
                        <th>Aircraft</th>
                        <th>Load</th>
                        <th>Effective Date</th>
                        <th>Added By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processedFlights as $flightPair)
                    <tr>
                        <td>
                            @foreach ($flightPair['days'] as $dayName => $status)
                            @if ($status)
                            <span class="badge bg-label-success me-1">{{ $dayName }}</span>
                            @else
                            <span class="badge bg-label-secondary me-1">{{ $dayName }}</span>
                            @endif
                            @endforeach
                        </td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['flight_number'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['from'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['to'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['std'] ?? '' }} {{ isset($flightPair['inbound']['std_local']) ?
                            '('.$flightPair['inbound']['std_local'].')' : ''
                            }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['sta'] ?? '' }} {{ isset($flightPair['inbound']['sta']) ?
                            '('.$flightPair['inbound']['sta_local'].')' : '' }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['aircraft_type'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['inbound']['active']) && $flightPair['inbound']['active']) bg-success-subtle @elseif(isset($flightPair['inbound']['active']) && !$flightPair['inbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['inbound']['capacity'] ?? '' }}</td>
                        <td></td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['flight_number'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['from'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['to'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['std'] ?? '' }} {{ isset($flightPair['outbound']['std_local']) ?
                            '('.$flightPair['outbound']['std_local'].')' : ''
                            }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['sta'] ?? '' }} {{ isset($flightPair['outbound']['sta']) ?
                            '('.$flightPair['outbound']['sta_local'].')' : '' }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['aircraft_type'] ?? '' }}</td>
                        <td
                            class="@if(isset($flightPair['outbound']['active']) && $flightPair['outbound']['active']) bg-success-subtle @elseif(isset($flightPair['outbound']['active']) && !$flightPair['outbound']['active']) bg-danger-subtle @endif">
                            {{ $flightPair['outbound']['capacity'] ?? '' }}</td>
                        <td>{{ $flightPair['effective_date'] }}</td>
                        <td>{{ $flightPair['added_by'] }}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit flights')
                                <a href="{{ route('maintenance.flight.show', $flightPair['flight_pair_id']) }}">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-info" title="View Flight">
                                        <i class="ti ti-eye ti-sm mx-2 text-primary"></i>
                                    </span>
                                </a>
                                <a
                                    href="{{ route('maintenance.flight.list', array_merge(request()->query(), ['clone_id' => $flightPair['flight_pair_id']])) }}">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-info" title="Clone Flight">
                                        <i class="ti ti-copy ti-sm mx-2 text-primary"></i>
                                    </span>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->
@elseif(request()->location)
<div class="card mt-2">
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            No flights found.
        </div>
    </div>
</div>
@endif

@can('add flights')
<div class="card mt-2">
    <div class="card-header bg-primary text-white text-center p-2">
        {{ isset($clonedFlight) ? 'Clone Flight' : 'Add Flight' }}
    </div>
    <div class="card-body">

        <form action="{{ route('maintenance.flight.store', request()->query()) }}" id="addFlightForm" method="POST">
            @csrf

            <div class="row mt-2">

                <!-- Inbound Flight -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white text-center p-2">
                            Inbound Flight
                        </div>
                        <div class="card-body p-2" id="inboundForm">
                            <div class="mb-2">
                                <label class="form-label">Flight:</label>
                                <input type="text" name="i_flight" class="form-control"
                                    value="{{ old('i_flight', $clonedFlight['inbound']['flight_number'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">From:</label>
                                <select class="form-select select2" name="i_from" id="i_from">
                                    <option value="">-- Select Airport --</option>
                                    @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}" @if(old('i_from',
                                        $clonedFlight['inbound']['from_id'] ?? '' )==$airport->id) selected @endif
                                        >{{ $airport->iata }} : {{ $airport->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">To:</label>
                                <select class="form-select select2" name="i_to" id="i_to">
                                    <option value="">-- Select Airport --</option>
                                    @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}" @if(old('i_to', $clonedFlight['inbound']['to_id']
                                        ?? '' )==$airport->id) selected @endif
                                        >{{ $airport->iata }} : {{ $airport->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">STD:</label>
                                <input type="time" name="i_departure_time" class="form-control time24"
                                    value="{{ old('i_departure_time', $clonedFlight['inbound']['std'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">STA:</label>
                                <input type="time" name="i_arrival_time" class="form-control time24"
                                    value="{{ old('i_arrival_time', $clonedFlight['inbound']['sta'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Aircraft:</label>
                                <select class="form-select select2" name="i_aircraft_type">
                                    <option value="">-- Select Aircraft --</option>
                                    @foreach ($aircraftTypes as $aircraftType)
                                    <option value="{{ $aircraftType->id }}" @if(old('i_aircraft_type',
                                        $clonedFlight['inbound']['aircraft_type_id'] ?? '' )==$aircraftType->id)
                                        selected
                                        @endif >{{ $aircraftType->formatted_name }} ({{ $aircraftType->capacity ?? 0
                                        }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row col-md-4">

                    <!-- Direction Type -->
                    <div class="col-md-12 text-center">
                        <h5 class="mb-3">Direction Type</h5>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="direction" value="both"
                                @if(old('direction', $clonedFlight['direction'] ?? 'both' )==='both' ) checked @endif>
                            <label class="form-check-label">Inbound & Outbound</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="direction" value="inbound"
                                @if(old('direction', $clonedFlight['direction'] ?? '' )==='inbound' ) checked @endif>
                            <label class="form-check-label">Inbound Only</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="direction" value="outbound"
                                @if(old('direction', $clonedFlight['direction'] ?? '' )==='outbound' ) checked @endif>
                            <label class="form-check-label">Outbound Only</label>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-header bg-primary text-white text-center p-2">
                                Days Active
                            </div>
                            <div class="card-body p-2">
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="1" @if(in_array(1, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Monday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="2" @if(in_array(2, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Tuesday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="3" @if(in_array(3, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Wednesday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="4" @if(in_array(4, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Thursday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="5" @if(in_array(5, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Friday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="6" @if(in_array(6, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Saturday</div>
                                <div class="form-check"><input type="checkbox" class="form-check-input" name="day[]"
                                        value="7" @if(in_array(7, old('day', $clonedFlight['days'] ?? []))) checked
                                        @endif> Sunday</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-header bg-primary text-white text-center p-2">
                                Effective From Date
                            </div>
                            <div class="card-body text-center p-2">
                                <input type="date" name="effective_date" id="effective_date" class="form-control"
                                    min="{{ date('Y-m-d') }}" value="{{ old('effective_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2 text-center">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($clonedFlight) ? 'Clone Flight' : 'Add Flight' }}
                        </button>
                    </div>

                </div>


                <!-- Outbound Flight -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white text-center p-2">
                            Outbound Flight
                        </div>
                        <div class="card-body p-2" id="outboundForm">
                            <div class="mb-2">
                                <label class="form-label">Flight:</label>
                                <input type="text" name="o_flight" class="form-control"
                                    value="{{ old('o_flight', $clonedFlight['outbound']['flight_number'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">From:</label>
                                <select class="form-select select2" name="o_from" id="o_from">
                                    <option value="">-- Select Airport --</option>
                                    @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}" @if(old('o_from',
                                        $clonedFlight['outbound']['from_id'] ?? '' )==$airport->id) selected @endif
                                        >{{ $airport->iata }} : {{ $airport->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">To:</label>
                                <select class="form-select select2" name="o_to" id="o_to">
                                    <option value="">-- Select Airport --</option>
                                    @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}" @if(old('o_to',
                                        $clonedFlight['outbound']['to_id'] ?? '' )==$airport->id) selected @endif
                                        >{{ $airport->iata }} : {{ $airport->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">STD:</label>
                                <input type="time" name="o_departure_time" class="form-control time24"
                                    value="{{ old('o_departure_time', $clonedFlight['outbound']['std'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">STA:</label>
                                <input type="time" name="o_arrival_time" class="form-control time24"
                                    value="{{ old('o_arrival_time', $clonedFlight['outbound']['sta'] ?? '') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Aircraft:</label>
                                <select class="form-select select2" name="o_aircraft_type">
                                    <option value="">-- Select Aircraft --</option>
                                    @foreach ($aircraftTypes as $aircraftType)
                                    <option value="{{ $aircraftType->id }}" @if(old('o_aircraft_type',
                                        $clonedFlight['outbound']['aircraft_type_id'] ?? '' )==$aircraftType->id)
                                        selected
                                        @endif >{{ $aircraftType->formatted_name }} ({{ $aircraftType->capacity ?? 0
                                        }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
@endcan

@endsection