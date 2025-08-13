<!-- To Field -->
<div class="mb-2">
    <strong>To:</strong>
    <div class="border rounded p-2 overflow-auto" style="max-height: 80px;">
        @foreach ($data as $item)
        <span class="d-block">@if(!empty($item['name'])) "{{ $item['name'] }}" @endif &lt;{{ $item['email']
            }}&gt;</span>
        @endforeach
    </div>
</div>

<!-- Subject Field -->
<div class="mb-2">
    <strong>Subject:</strong>
    <span>Bridges WW Update for Flight {{ $scheduleFlight->flight->flight_number }} on {{
        \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</span>
</div>

<!-- Email Content -->
<div class="border rounded p-2 overflow-auto" style="max-height: 420px;">
    <h6 class="text-center fw-bold">FLIGHT REPORT</h6>
    <p class="text-center"><strong>Date:</strong> {{
        \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}</p>
    <p class="text-center"><strong>Flight Number:</strong> {{ $scheduleFlight->flight->flight_number }}</p>

    <div class="table-responsive">
        <table class="table table-sm table-bordered text-center w-100">
            <thead>
                <tr>
                    <th>Route</th>
                    <th>STD*</th>
                    <th>ETD*</th>
                    <th>ATD*</th>
                    <th>+/- Mins</th>
                    <th>STA*</th>
                    <th>ETA*</th>
                    <th>ATA*</th>
                    <th>Uplifted</th>
                    <th>Offloaded</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $scheduleFlight->flight->fromAirport->iata }} - {{ $scheduleFlight->flight->toAirport->iata
                        }}
                    </td>
                    <td>{{ $scheduleFlight->flight->departure_time_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlight->etd_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlight->atd_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlight->departure_time_diff }}</td>
                    <td>{{ $scheduleFlight->flight->arrival_time_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlight->eta_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlight->ata_local ?? 'None' }}</td>
                    <td>{{ $scheduleFlightCustomer->total_uplifted_weight ?? 0 }} Kgs</td>
                    <td>{{ $scheduleFlight->total_offloaded_weight ?? 0 }} Kgs</td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <p class="fst-italic">*All Times Local</p>

    <hr>
    <p class="fw-bold">Service Remarks:</p>
    @foreach ($serviceRemarks as $serviceRemark)
    <p>{{ $serviceRemark }}</p>
    @endforeach

    <hr>

    <p class="text-muted"><strong>Bridges Flight Coordination Centre</strong></p>
    <p>Email: <a href="mailto:bridges.ops.centre@bridgesww.com">bridges.ops.centre@bridgesww.com</a></p>
    <p>Phone: +359 2 840 66 69 / +359 2 841 24 33</p>
    <p>Fax: +359 2 841 24 32</p>
</div>