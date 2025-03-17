<!-- Email Content -->
<div class="border rounded p-2 overflow-auto" style="max-height: 420px;">
    <h6 class="text-center fw-bold">FLIGHT REPORT</h6>
    <p class="text-center"><strong>Date:</strong> {{ $data['date'] }}</p>
    <p class="text-center"><strong>Flight Number:</strong> {{ $data['flight_number'] }}</p>

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
                    <td>{{ $data['route'] }}
                    </td>
                    <td>{{ $data['std'] }}</td>
                    <td>{{ $data['etd'] }}</td>
                    <td>{{ $data['atd'] }}</td>
                    <td>{{ $data['departure_diff'] }}</td>
                    <td>{{ $data['sta'] }}</td>
                    <td>{{ $data['eta'] }}</td>
                    <td>{{ $data['ata'] }}</td>
                    <td>{{ $data['uplifted'] }} Kgs</td>
                    <td>{{ $data['offloaded'] }} Kgs</td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <p class="fst-italic">*All Times Local</p>

    <hr>
    <p class="fw-bold">Service Remarks:</p>
    @foreach ($data['service_remarks'] as $serviceRemark)
    <p>{{ $serviceRemark }}</p>
    @endforeach

    <hr>

    <p class="text-muted"><strong>Bridges Flight Coordination Centre</strong></p>
    <p>Email: <a href="mailto:bridges.ops.centre@bridgesww.com">bridges.ops.centre@bridgesww.com</a></p>
    <p>Phone: +359 2 840 66 69 / +359 2 841 24 33</p>
    <p>Fax: +359 2 841 24 32</p>
</div>