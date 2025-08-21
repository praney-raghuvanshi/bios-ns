@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Billing Extract')

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
<script src="{{asset('assets/js/billing-extract/report.js')}}"></script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Billing Extract
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
        <h5>Billing Extract</h5>
    </div>
    <div class="card-body">
        <form id="billingExtractForm" method="POST" action="{{ route('reports.billing-extract.list') }}">
            @csrf

            <!-- First Row: Year, Start Week, End Week -->
            <div class="row">

                <div class="col-md-3">
                    <label class="form-label" for="startDate">Start Date</label>
                    <input type="date" name="start_date" id="startDate" class="form-select"
                        value="{{ request('start_date', \Carbon\Carbon::yesterday()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="endDate">End Date</label>
                    <input type="date" name="end_date" id="endDate" class="form-select"
                        value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="zone">Zone</label>
                    <select name="zone[]" id="zone" class="form-select select2" multiple>
                        <option value="">-- Select Zone --</option>

                        {{-- All Zones option --}}
                        <option value="all" @if(collect(request('zone'))->contains('all') || !request()->has('zone'))
                            selected @endif>
                            All Zones
                        </option>

                        {{-- Individual Zones --}}
                        @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" @if(collect(request('zone'))->contains($zone->id)) selected
                            @endif>
                            {{ $zone->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="flight">Flight</label>
                    <select name="flight[]" id="flight" class="form-select select2" multiple>
                        <option value="">-- Select Flight --</option>

                        {{-- All Flights option --}}
                        <option value="all" @if(collect(request('flight'))->contains('all') ||
                            !request()->has('flight')) selected @endif>
                            All Flights
                        </option>

                        {{-- Individual Flights --}}
                        @foreach ($flights as $flight)
                        <option value="{{ $flight }}" @if(collect(request('flight'))->contains($flight)) selected
                            @endif>
                            {{ $flight }}
                        </option>
                        @endforeach
                    </select>

                </div>
            </div>

            <!-- Run Report Button -->
            <div class="row mt-3">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary" id="billingExtractRunBtn">Run Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(count($finalData) > 0)
<div class="card mt-2">
    <div class="card-header text-center">
        <form action="{{ route('reports.billing-extract.export') }}" method="POST">
            @csrf
            <input type="hidden" name="export_start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="export_end_date" value="{{ request('end_date') }}">

            {{-- Zones --}}
            @if(request()->has('zone'))
            @foreach((array) request('zone') as $z)
            <input type="hidden" name="export_zone[]" value="{{ $z }}">
            @endforeach
            @endif

            {{-- Flights --}}
            @if(request()->has('flight'))
            @foreach((array) request('flight') as $f)
            <input type="hidden" name="export_flight[]" value="{{ $f }}">
            @endforeach
            @endif


            <button type="submit" class="btn btn-primary">
                <i class="fas fa-file-excel me-2"></i> Export
            </button>
        </form>
    </div>
    <div class="card-body p-2">
        <table>
            <tr class="header">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>CON1</th>
                <th>CON2</th>
                <th>CON3</th>
                <th>CON4</th>
                <th>Declared</th>
                <th>Actual</th>
                <th>Volume</th>
                <th>Total Actual</th>
                <th>Total Volume</th>
                <th>First/Subs</th>
                <th colspan="3">CUSTOMER DETAIL</th>
                <th>CHARGES</th>
            </tr>
            <tr class="header">
                <th>DATE</th>
                <th>ORIGIN</th>
                <th>DEST</th>
                <th>END DEST</th>
                <th>FLIGHT #</th>
                <th>AIRWAY BILL #</th>
                <th>LD3</th>
                <th>LD7</th>
                <th></th>
                <th></th>
                <th>KG</th>
                <th>KG</th>
                <th>KG</th>
                <th>KG</th>
                <th>KG</th>
                <th>SHIPMENT</th>
                <th>CON #</th>
                <th>CUSTOMER</th>
                <th>PRODUCT</th>
                <th>HANDLING</th>
            </tr>
            @foreach ($finalData as $datum)
            <tr>
                <td>{{ $datum['date'] }}</td>
                <td>{{ $datum['origin'] }}</td>
                <td>{{ $datum['destination'] }}</td>
                <td>{{ $datum['end_destination'] }}</td>
                <td>{{ $datum['flight'] }}</td>
                <td>{{ $datum['awb'] }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $datum['declared'] }}</td>
                <td>{{ $datum['actual'] }}</td>
                <td>{{ $datum['volume'] }}</td>
                <td>{{ $datum['total_actual'] }}</td>
                <td>{{ $datum['total_volume'] }}</td>
                <td>{{ $datum['shipment_type'] }}</td>
                <td></td>
                <td>{{ $datum['customer'] }}</td>
                <td>{{ $datum['product'] }}</td>
                <td></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@else
@if(request()->isMethod('post'))
<div class="card mt-2">
    <div class="card-body">
        <div class="alert alert-danger" role="alert">
            <strong>No Results </strong> - There are no records that match your filters!
        </div>
    </div>
</div>
@endif
@endif

@endsection