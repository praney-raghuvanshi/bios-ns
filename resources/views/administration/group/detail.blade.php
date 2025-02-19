@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Group Detail')

@section('page-script')
<script src="{{asset('assets/js/group/edit.js')}}"></script>

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
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Group Detail
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
    <div class="card-body d-flex justify-content-between align-items-center">
        <div class="align-self-center">
            <span class="btn btn-md btn-primary"><strong>{{ $group->name }}</strong></span>
            <span class="btn btn-md btn-info">Added By: &nbsp; <strong> {{ $group->addedByUser->name }}</strong></span>
        </div>
        <div>
            <a href="{{ route('administration.group.list') }}" class="btn btn-secondary"><i
                    class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Group List</a>
            @can('edit groups')
            <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#editGroup">
                <span><i class="ti ti-pencil me-0 me-sm-1 ti-xs"></i>Edit Group</span> </button>
            @endcan
        </div>
    </div>
</div>

@can('view roles')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Group Roles</h5>
        @if($group->active)
        @can('add roles')
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageRolesForGroup">
            <span><i class="ti ti-shield-lock me-0 me-sm-1 ti-xs"></i>Manage Roles</span>
        </button>
        @endcan
        @endif
    </div>
    <div class="card-body">
        <!-- Roles List -->
        @if(count($groupRoles) > 0)
        <div class="d-flex flex-wrap gap-2">
            @foreach($groupRoles as $role)
            <button type="button" class="btn btn-outline-warning">
                {{ $role }}
            </button>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted">No roles are assigned to this group.</p>
        @endif
    </div>
</div>
@endcan

@can('view users')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Group Users</h5>
        @if($group->active)
        @can('add users')
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageUsersForGroup">
            <span><i class="ti ti-user-edit me-0 me-sm-1 ti-xs"></i>Manage Users</span>
        </button>
        @endcan
        @endif
    </div>
    <div class="card-body">
        <!-- Users List -->
        @if($groupUsers->count() > 0)
        <div class="d-flex flex-wrap gap-2">
            @foreach($groupUsers as $user)
            <button type="button" class="btn btn-outline-success">
                {{ $user->name }}
            </button>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted">No users are assigned to this group.</p>
        @endif
    </div>
</div>
@endcan

@include('_partials/_modals/group/edit')

@include('_partials/_modals/group/role')

@include('_partials/_modals/group/user')

@endsection