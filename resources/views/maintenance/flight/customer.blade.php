@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Flight Day Customers')

@section('page-script')

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
    <span class="text-muted fw-light">BIOS /</span> Flight Day Customers
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
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div class="align-self-center">

        </div>
        <div>
            <a href="{{ route('maintenance.flight.show', $flight->flight_pair_id) }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Back
            </a>
            <a href="{{ route('maintenance.flight.list') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Flight List
            </a>
        </div>
    </div>
</div>

@can('view customers')
<div class="card mt-2">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Flight Day Customers</h5>
        @can('edit customers')
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageCustomersForFlightDay">
            <span><i class="ti ti-users me-0 me-sm-1 ti-xs"></i>Manage Customers</span>
        </button>
        @endcan
    </div>
    <div class="card-body p-3">
        <!-- Customers List -->
        @if(count($linkedCustomers) > 0)
        <div class="d-flex flex-wrap gap-2">
            @foreach($linkedCustomers as $customer)
            <button type="button" class="btn btn-outline-success">
                {{ $customer }}
            </button>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted">No customers are assigned to this flight day.</p>
        @endif
    </div>
</div>
@endcan

@include('_partials/_modals/flight/day/customer')

@endsection