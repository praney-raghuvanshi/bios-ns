@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'User List')

@section('page-script')

<script src="{{asset('assets/js/user/add.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#users-table thead tr').clone(true).appendTo('#users-table thead');

    var dt = $('#users-table').DataTable({
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
                    $('#users-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                targets: [0, 2], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#users-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        if (![0,2].includes(i)) { // Exclude the "Actions" column from searching
          $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
          $(this).html('<input type="text" class="form-control" disabled />');
        }

        $('input', this).on('keyup change', function () {
            if (![0,2].includes(i) && dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });

    $('.edit-btn').on('click', function () {
        let userId = $(this).data('record-id');

        // Load the edit form content into the modal
        $.ajax({
            url: '{{ route("administration.user.edit", ":userId") }}'.replace(':userId', userId),
            type: 'GET',
            success: function (response) {
                $('#editUser .modal-form').html(response);
                $('#editUser').show();

                // Initialize jQuery Validation
                $('#editUserForm').validate({
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
                        email: {
                            required: true,
                            email: true
                        },
                        group: {
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
    <span class="text-muted fw-light">BIOS /</span> Users
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
        <h5>Users</h5>
        @can('add users')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">
            <span><i class="ti ti-plus ti-xs"></i>Add New User</span>
        </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="users-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit users')
                                <a href="javascript:;" class="text-body edit-btn" data-record-id="{{ $user->id }}"
                                    data-bs-toggle="modal" data-bs-target="#editUser"><i
                                        class="ti ti-edit ti-sm text-info"></i></a>
                                @endcan
                            </div>
                        </td>
                        <td>{{ $user->formatted_id }}</td>
                        <td>
                            @if($user->profile_image)
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="avatar-wrapper">
                                    <div class="avatar">
                                        <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt
                                            class="h-auto rounded-circle">
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="avatar-wrapper">
                                    <div class="avatar">
                                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                            class="h-auto rounded-circle">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->group->name ?? 'N/A' }}</td>
                        <td>
                            @if ($user->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if (!is_null($user->last_login_at))
                            {{ Carbon\Carbon::parse($user->last_login_at)->format('d-m-Y H:i:s') }}
                            @else
                            N/A
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@include('_partials/_modals/user/add')

<!-- Edit Modal -->
<div class="modal" id="editUser" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit User</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection