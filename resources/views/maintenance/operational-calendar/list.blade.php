@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Operational Calendar List')

@section('page-script')

<script src="{{asset('assets/js/operational-calendar/add.js')}}"></script>
<script src="{{asset('assets/js/operational-calendar/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#operational-calendars-table thead tr').clone(true).appendTo('#operational-calendars-table thead');

    var dt = $('#operational-calendars-table').DataTable({
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
                    $('#operational-calendars-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                type: 'string',
                targets: [2, 3, 4]
            },
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#operational-calendars-table thead tr:eq(1) th').each(function (i) {
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

    // $('.edit-btn').on('click', function () {
    //     let operationalCalendarId = $(this).data('record-id');

    //     // Load the edit form content into the modal
    //     $.ajax({
    //         url: ''.replace(':operationalCalendarId', operationalCalendarId),
    //         type: 'GET',
    //         success: function (response) {
    //             $('#editOperationalCalendar .modal-form').html(response);
    //             $('#editOperationalCalendar').show();

    //             // Initialize jQuery Validation
    //             $('#editOperationalCalendarForm').validate({
    //                 errorElement: 'div',
    //                 errorClass: 'invalid-feedback',
    //                 highlight: function (element, errorClass, validClass) {
    //                     $(element).addClass('is-invalid').removeClass('is-valid');
    //                 },
    //                 unhighlight: function (element, errorClass, validClass) {
    //                     $(element).removeClass('is-invalid');
    //                 },
    //                 errorPlacement: function (error, element) {
    //                     error.appendTo(element.parent().append('<div class="form-control-feedback"></div>'));
    //                 },
    //                 rules: {
    //                     year: {
    //                         required: true
    //                     },
    //                     start_date: {
    //                         required: true
    //                     },
    //                     weeks: {
    //                         required: true
    //                     }
    //                 }
    //             });
    //         }
    //     });
    // });
});
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Operational Calendars
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
        <h5>Operational Calendars</h5>
        @can('add operational-calendars')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOperationalCalendar">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Operational Calendar</span>
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="operational-calendars-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Year</th>
                        <th>Start Date</th>
                        <th>Weeks</th>
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operationalCalendars as $operationalCalendar)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                {{-- @can('edit operational-calendars')
                                <a href="javascript:;" class="text-body edit-btn"
                                    data-record-id="{{ $operationalCalendar->id }}" data-bs-toggle="modal"
                                    data-bs-target="#editOperationalCalendar"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan --}}
                                @can('delete operational-calendars')
                                <form
                                    action="{{ route('maintenance.operational-calendar.destroy', $operationalCalendar) }}"
                                    method="post" id="delete-operational-calendar-form-{{ $operationalCalendar->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmOperationalCalendarDelete({{ $operationalCalendar->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                        <td>{{ $operationalCalendar->formatted_id }}</td>
                        <td>{{ $operationalCalendar->year }}</td>
                        <td>{{ Carbon\Carbon::parse($operationalCalendar->start_date)->format('d-m-Y') }}</td>
                        <td>{{ $operationalCalendar->weeks }}</td>
                        <td>
                            @if ($operationalCalendar->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $operationalCalendar->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@include('_partials/_modals/operational-calendar/add')

@endsection