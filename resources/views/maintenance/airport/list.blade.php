@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Airport List')

@section('page-script')

<script src="{{asset('assets/js/airport/add.js')}}"></script>
<script src="{{asset('assets/js/airport/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#airports-table thead tr').clone(true).appendTo('#airports-table thead');

    var dt = $('#airports-table').DataTable({
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
                    $('#airports-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: true
            }
        ]
    });

    $('#airports-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        if (![0].includes(i)) { // Exclude the "Actions" column from searching
          $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
          $(this).html('<input type="text" class="form-control" disabled />');
        }

        $('input', this).on('keyup change', function () {
            if (![0].includes(i) && dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });
});

$(document).on('click', '.edit-btn', function () {
        let airportId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("maintenance.airport.edit", ":airportId") }}'.replace(':airportId', airportId),
            type: 'GET',
            success: function (response) {
                $('#editAirport .modal-form').html(response);
                $('#editAirport').show();

                const select2 = $('.select2');

                if (select2.length) {
                    select2
                    .each(function () {
                        var $this = $(this);
                        $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent()
                        });
                    })
                    .on('change', function () {
                        $(this).valid();
                    });
                }

                // Initialize jQuery Validation
                $('#editAirportForm').validate({
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
                        iata: {
                            required: true,
                            minlength: 3,
                            maxlength: 3
                        },
                        name: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        country: {
                            required: true
                        },
                        timezone: {
                            required: true
                        },
                        // summer_difference: {
                        //     required: true,
                        //     number: true
                        // },
                        // winter_difference: {
                        //     required: true,
                        //     number: true
                        // },
                        status: {
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
    <span class="text-muted fw-light">BIOS /</span> Airports
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

<!-- Bordered Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Airports</h5>
        @can('add airports')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAirport">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Airport</span>
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="airports-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>IATA</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Timezone</th>
                        {{-- <th>Summer Difference</th>
                        <th>Winter Difference</th> --}}
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($airports as $airport)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit airports')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $airport->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editAirport"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                {{-- @can('delete airports')
                                <form action="{{ route('maintenance.airport.destroy', $airport) }}" method="post"
                                    id="delete-airport-form-{{ $airport->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmAirportDelete({{ $airport->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan --}}
                            </div>
                        </td>
                        <td>{{ $airport->formatted_id }}</td>
                        <td>{{ $airport->iata }}</td>
                        <td>{{ $airport->name }}</td>
                        <td>{{ $airport->city }}</td>
                        <td>{{ $airport->country }}</td>
                        <td>{{ $airport->timezone }}</td>
                        {{-- <td>{{ $airport->summer_difference }}</td>
                        <td>{{ $airport->winter_difference }}</td> --}}
                        <td>
                            @if ($airport->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $airport->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@include('_partials/_modals/airport/add')

<!-- Edit Modal -->
<div class="modal" id="editAirport" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Airport</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection