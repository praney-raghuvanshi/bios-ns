@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Daily Flight Report')

@section('page-style')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto;
    }

    th,
    td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
    }

    .header {
        font-weight: bold;
        background-color: #f4f4f4;
    }
</style>
@endsection

@section('page-script')
<script src="{{asset('assets/js/daily-flight-report/report.js')}}"></script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Daily Flight Report
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
    <div class="card-header">
        <h5>Daily Flight Report</h5>
    </div>
    <div class="card-body">
        <form id="dailyFlightReportForm" method="POST" action="{{ route('reports.daily-flight-report.list') }}">
            @csrf

            <div class="d-flex align-items-end gap-3">
                <div>
                    <input type="date" name="flight_date" id="flightDate" class="form-select"
                        value="{{ request('flight_date') }}" placeholder="Flight Date" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" id="dailyFlightReportRunBtn">Run Report</button>
                </div>
            </div>
        </form>
    </div>

</div>

@if(count($finalReport) > 0)
<div class="card mt-2">
    <div class="card-header text-center">
        {{-- <form action="{{ route('reports.daily-flight-report.export') }}" method="POST">
            @csrf
            <input type="hidden" name="export_flight_date" value="{{ request('flight_date') }}">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-file-excel me-2"></i> Export
            </button>
        </form> --}}
    </div>
    <div class="card-body p-2">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="header">
                    <th>Date</th>
                    <th>Flight</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>STD</th>
                    <th>ATD</th>
                    <th>+/-</th>
                    <th>STA</th>
                    <th>ATA</th>
                    <th>+/-</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Uplifted Weight (kg)</th>
                    <th>Offloaded Weight (kg)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($finalReport as $flight)
                @foreach ($flight['shipments'] as $shipment)
                <tr>
                    <td>{{ $flight['date'] }}</td>
                    <td>{{ $flight['flight_number'] }}</td>
                    <td>{{ $flight['origin'] }}</td>
                    <td>{{ $flight['destination'] }}</td>
                    <td>{{ $flight['std'] }}</td>
                    <td>{{ $flight['atd'] }}</td>
                    <td>{{ $flight['departure_time_difference'] ?? 0 }}</td>
                    <td>{{ $flight['sta'] }}</td>
                    <td>{{ $flight['ata'] }}</td>
                    <td>{{ $flight['arrival_time_difference'] ?? 0 }}</td>
                    <td>{{ $shipment['customer'] }}</td>
                    <td>{{ $shipment['product'] }}</td>
                    <td>{{ $shipment['uplifted_weight'] }}</td>
                    <td>{{ $shipment['offloaded_weight'] }}</td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="12">No shipment data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
@if(request()->isMethod('post'))
<div class="card mt-2">
    <div class="card-body">
        <div class="alert alert-danger" role="alert">
            <strong>No Results </strong> - There are no records that match your filter!
        </div>
    </div>
</div>
@endif
@endif

@endsection