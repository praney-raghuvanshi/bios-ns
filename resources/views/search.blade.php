@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Search')

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

    @if(count($data) > 0)
    <div class="col-12 col-xl-12 col-md-12 col-sm-12">
        <div class="alert alert-success" role="alert">
            Search Results for <strong> {{ $query }} </strong>
        </div>

        <div class="row">
            @foreach ($data as $item)
            <div class="col-4 mb-3">
                <div class="card">
                    <div class="card-header align-items-center">
                        <div class="d-flex justify-content-between">
                            <div class="text-primary">AWB: <strong>{{ $item->awb }}</strong></div>
                            <div class="btn btn-sm btn-success">
                                {{ $item->product->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group mt-1">
                            <li class="list-group-item d-flex align-items-center text-dark">
                                <i class="ti ti-user ti-sm me-2"></i>
                                {{ $item->scheduleFlightCustomer->customer->name ?? 'N/A' }}
                            </li>
                            <li class="list-group-item d-flex align-items-center text-dark">
                                <i class="ti ti-calendar-time ti-sm me-2"></i>
                                Date : {{
                                \Carbon\Carbon::parse($item->scheduleFlight->schedule->date)->format('d-m-Y') }}
                            </li>
                            <li class="list-group-item d-flex align-items-center text-dark">
                                <i class="ti ti-file ti-sm me-2"></i>
                                Flight : {{ $item->scheduleFlight->flight->flight_number ?? 'N/A' }}
                            </li>
                            <li class="list-group-item d-flex align-items-center text-dark">
                                <i class="ti ti-plane-departure ti-sm me-2"></i>
                                Origin : {{ $item->scheduleFlight->flight->fromAirport->iata ?? 'N/A' }}
                            </li>
                            <li class="list-group-item d-flex align-items-center text-dark">
                                <i class="ti ti-plane-arrival ti-sm me-2"></i>
                                Destination : {{ $item->scheduleFlight->flight->toAirport->iata ?? 'N/A' }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="col-12 col-xl-12 col-md-12 col-sm-12">
        <div class="alert alert-danger" role="alert">
            <strong>No Results </strong> - There are no records that match your search query : <strong>{{ $query
                }}</strong>.
        </div>
    </div>
    @endif

</div>

@endsection