@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')

@endsection

@section('page-style')

@endsection

@section('vendor-script')

@endsection

@section('page-script')

@endsection

@section('content')

<div class="row">

    <!-- Customers -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-1">Customers</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="d-flex gap-4 align-items-center mb-2">
                            <span class="badge bg-label-info p-1 rounded"><i class="ti ti-users ti-xs"></i></span>
                            <h5 class="mb-0 pt-1 text-nowrap">{{ $customersCount ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Customers -->

    <!-- Products -->
    <div class="col-lg-3 col-sm-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-1">Products</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="d-flex gap-4 align-items-center mb-2">
                            <span class="badge bg-label-info p-1 rounded"><i
                                    class="ti ti-brand-producthunt ti-xs"></i></span>
                            <h5 class="mb-0 pt-1 text-nowrap">{{ $productsCount ?? 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Products -->

</div>

@endsection