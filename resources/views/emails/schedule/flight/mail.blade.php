<!-- Email Content -->
<div
    style="border: 1px solid #ccc; border-radius: 4px; padding: 10px; max-height: 420px; overflow-y: auto; font-family: Arial, sans-serif; font-size: 14px; color: #000; line-height: 1.4;">

    <h6 style="text-align: center; font-weight: bold; font-size: 16px; margin: 0 0 8px 0; padding: 0;">FLIGHT REPORT
    </h6>

    <p style="text-align: center; margin: 5px 0;"><strong>Date:</strong> {{ $data['date'] }}</p>
    <p style="text-align: center; margin: 5px 0;"><strong>Flight Number:</strong> {{ $data['flight_number'] }}</p>

    <table cellpadding="5" cellspacing="0" width="100%"
        style="border: 1px solid #ccc; border-collapse: collapse; text-align: center; font-size: 13px;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="border: 1px solid #ccc; padding: 5px;">Route</th>
                <th style="border: 1px solid #ccc; padding: 5px;">STD*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">ETD*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">ATD*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">+/- Mins</th>
                <th style="border: 1px solid #ccc; padding: 5px;">STA*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">ETA*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">ATA*</th>
                <th style="border: 1px solid #ccc; padding: 5px;">Uplifted</th>
                <th style="border: 1px solid #ccc; padding: 5px;">Offloaded</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['route'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['std'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['etd'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['atd'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['departure_diff'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['sta'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['eta'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['ata'] }}</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['uplifted'] }} Kgs</td>
                <td style="border: 1px solid #ccc; padding: 5px;">{{ $data['offloaded'] }} Kgs</td>
            </tr>
        </tbody>
    </table>

    <p style="font-style: italic; margin-top: 10px; margin-bottom: 10px;">*All Times Local</p>

    <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">

    <p style="font-weight: bold; margin-bottom: 5px;">Service Remarks:</p>
    @if(count($data['service_remarks']) > 0)
    @foreach ($data['service_remarks'] as $serviceRemark)
    <p style="margin: 3px 0;">{{ $serviceRemark }}</p>
    @endforeach
    @else
    <p style="margin: 3px 0;">None</p>
    @endif

    <hr style="border: none; border-top: 1px solid #ccc; margin: 10px 0;">

    <p style="color: #555; font-weight: bold; margin: 5px 0;">Bridges Flight Coordination Centre</p>
    <p style="margin: 3px 0;">Email:
        <a href="mailto:bridges.ops.centre@bridgesww.com" style="color: #0066cc; text-decoration: none;">
            bridges.ops.centre@bridgesww.com
        </a>
    </p>
    <p style="margin: 3px 0;">Phone: +359 2 840 66 69 / +359 2 841 24 33</p>
    <p style="margin: 3px 0;">Fax: +359 2 841 24 32</p>
</div>