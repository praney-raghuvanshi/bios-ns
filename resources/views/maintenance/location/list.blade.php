@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Location List')

@section('page-script')

<script src="{{asset('assets/js/location/add.js')}}"></script>
<script src="{{asset('assets/js/location/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#locations-table thead tr').clone(true).appendTo('#locations-table thead');

    var dt = $('#locations-table').DataTable({
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
                    $('#locations-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#locations-table thead tr:eq(1) th').each(function (i) {
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
        let locationId = $(this).data('record-id');

        $('#editLocation .modal-form').html('<p class="text-center my-3">Loading...</p>');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("maintenance.location.edit", ":locationId") }}'.replace(':locationId', locationId),
            type: 'GET',
            success: function (response) {
                $('#editLocation .modal-form').html(response);
                $('#editLocation').show();

                // Initialize jQuery Validation
                $('#editLocationForm').validate({
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
                        zone: {
                            required: true
                        },
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
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Locations
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
        <h5>Locations</h5>
        @can('add locations')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocation">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Location</span>
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="locations-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Zone</th>
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locations as $location)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit locations')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $location->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editLocation"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                {{-- @can('delete locations')
                                <form action="{{ route('maintenance.location.destroy', $location) }}" method="post"
                                    id="delete-location-form-{{ $location->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmLocationDelete({{ $location->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan --}}
                            </div>
                        </td>
                        <td>{{ $location->formatted_id }}</td>
                        <td>{{ $location->code }}</td>
                        <td>{{ $location->name }}</td>
                        <td>{{ $location->zone->name ?? 'N/A' }}</td>
                        <td>
                            @if ($location->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $location->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@include('_partials/_modals/location/add')

<!-- Edit Modal -->
<div class="modal" id="editLocation" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Location</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection