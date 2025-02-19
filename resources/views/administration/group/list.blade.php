@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Group List')

@section('page-script')
<script src="{{asset('assets/js/group/add.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#groups-table thead tr').clone(true).appendTo('#groups-table thead');

    var dt = $('#groups-table').DataTable({
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
                    $('#groups-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ]
    });

    $('#groups-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

        $('input', this).on('keyup change', function () {
            if (dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });
});
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Groups
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

@can('add groups')
<div class="card">
    <h5 class="card-header">Add Group</h5>
    <div class="card-body">
        <form id="addGroupForm" action="{{ route('administration.group.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="group_name" class="form-control" placeholder="Enter Group Name"
                        value="{{ old('group_name') }}" />
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-md btn-primary">Add Group</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- Bordered Table -->
<div class="card mt-3">
    <h5 class="card-header">Groups</h5>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="groups-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                    <tr>
                        <td>
                            @can('view groups')
                            <a href="{{ route('administration.group.show', $group) }}" class="text-primary">{{
                                $group->formatted_id
                                }}</a>
                            @else
                            {{ $group->formatted_id }}
                            @endcan
                        </td>
                        <td>{{ $group->name }}</td>
                        <td>
                            @if ($group->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $group->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@endsection