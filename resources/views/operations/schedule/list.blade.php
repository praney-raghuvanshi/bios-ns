@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Schedule List')

@section('page-script')

<script src="{{asset('assets/js/schedule/add.js')}}"></script>
<script src="{{asset('assets/js/schedule/delete.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#schedules-table thead tr').clone(true).appendTo('#schedules-table thead');

    var dt = $('#schedules-table').DataTable({
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
                    $('#schedules-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                type: 'string',
                targets: [3]
            },
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#schedules-table thead tr:eq(1) th').each(function (i) {
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
        let scheduleId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: ''.replace(':scheduleId', scheduleId),
            type: 'GET',
            success: function (response) {
                $('#editSchedule .modal-form').html(response);
                $('#editSchedule').show();

                // Initialize jQuery Validation
                $('#editScheduleForm').validate({
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

<!-- Bordered Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Schedules</h5>
        @can('add schedules')
        <a href="{{ route('flight-operations.schedule.create') }}" class="btn btn-primary">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Schedule</span>
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="schedules-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Description</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $schedule)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('view schedules')
                                <a href="{{ route('flight-operations.schedule.show', $schedule) }}">
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-custom-class="tooltip-info" title="View Schedule">
                                        <i class="ti ti-eye ti-sm mx-2 text-primary"></i>
                                    </span>
                                </a>
                                @endcan
                                @can('delete schedules')
                                <form action="{{ route('flight-operations.schedule.destroy', $schedule) }}"
                                    method="post" id="delete-schedule-form-{{ $schedule->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;" onclick="confirmScheduleDelete({{ $schedule->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                        <td>{{ $schedule->formatted_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</td>
                        <td>{{ Helper::getFlightDaysName($schedule->day) }}</td>
                        <td>{{ $schedule->description }}</td>
                        <td>{{ $schedule->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@endsection