@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Aircraft Type List')

@section('page-script')

<script src="{{asset('assets/js/aircraft-type/add.js')}}"></script>
<script src="{{asset('assets/js/aircraft-type/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#aircraft-types-table thead tr').clone(true).appendTo('#aircraft-types-table thead');

    var dt = $('#aircraft-types-table').DataTable({
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
                    $('#aircraft-types-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                type: 'string',
                targets: [3, 4] // Adjusted to include all relevant columns
            },
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#aircraft-types-table thead tr:eq(1) th').each(function (i) {
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

    $('.edit-btn').on('click', function () {
        let aircraftTypeId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("maintenance.aircraft-type.edit", ":aircraftTypeId") }}'.replace(':aircraftTypeId', aircraftTypeId),
            type: 'GET',
            success: function (response) {
                $('#editAircraftType .modal-form').html(response);
                $('#editAircraftType').show();

                // Initialize jQuery Validation
                $('#editAircraftTypeForm').validate({
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
                        aircraft_manufacturer: {
                            required: true
                        },
                        name: {
                            required: true
                        },
                        capacity: {
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
    <span class="text-muted fw-light">BIOS /</span> Aircraft Types
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
        <h5>Aircraft Types</h5>
        @can('add aircrafts')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAircraftType">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Aircraft Type</span>
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="aircraft-types-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Manufacturer</th>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aircraftTypes as $aircraftType)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit aircrafts')
                                <a href="javascript:;" class="text-body edit-btn"
                                    data-record-id="{{ $aircraftType->id }}" data-bs-toggle="modal"
                                    data-bs-target="#editAircraftType"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete aircrafts')
                                <form action="{{ route('maintenance.aircraft-type.destroy', $aircraftType) }}"
                                    method="post" id="delete-aircraft-type-form-{{ $aircraftType->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmAircraftTypeDelete({{ $aircraftType->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                        <td>{{ $aircraftType->formatted_id }}</td>
                        <td>{{ $aircraftType->aircraftManufacturer->name }}</td>
                        <td>{{ $aircraftType->name }}</td>
                        <td>{{ $aircraftType->capacity }}</td>
                        <td>
                            @if ($aircraftType->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $aircraftType->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@include('_partials/_modals/aircraft-type/add')

<!-- Edit Modal -->
<div class="modal" id="editAircraftType" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Aircraft Type</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection