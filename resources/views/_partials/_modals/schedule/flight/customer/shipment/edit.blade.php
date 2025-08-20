<!-- Edit Schedule Flight Cutomer AWB Modal -->

<form id="editScheduleFlightCustomerAwbForm" class="row g-3" method="POST"
    action="{{ route('flight-operations.schedule.flight.customer.shipment.update', [$schedule, $scheduleFlight, $scheduleFlightCustomer, $scheduleFlightCustomerShipment]) }}">
    @csrf

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="product">Product</label>
            <select name="product" class="select2 form-select" disabled>
                <option value="">-- Select Product --</option>
                @foreach ($scheduleFlightCustomer->scheduleFlightCustomerProducts as $item)
                <option value="{{$item->product_id}}" @if(old('product', $scheduleFlightCustomerShipment->
                    product_id)===$item->product_id) selected
                    @endif>{{ $item->product->formatted_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="destination">Destination</label>
            <select name="destination" class="select2 form-select" disabled>
                <option value="">-- Select Destination --</option>
                @foreach ($airports as $item)
                <option value="{{$item->id}}" @if(old('destination', $scheduleFlightCustomerShipment->
                    destination)===$item->id) selected
                    @endif>{{ $item->formatted_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="declaredWeight">Declared Weight</label>
            <input type="number" min="0" class="form-control" name="declared_weight"
                value="{{ old('declared_weight', $scheduleFlightCustomerShipment->declared_weight ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="actualWeight">Actual Weight</label>
            <input type="number" min="0" class="form-control" name="actual_weight"
                value="{{ old('actual_weight', $scheduleFlightCustomerShipment->actual_weight ?? 0) }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="volumetricWeight">Volumetric Weight</label>
            <input type="number" min="0" class="form-control" name="volumetric_weight"
                value="{{ old('volumetric_weight', $scheduleFlightCustomerShipment->volumetric_weight ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="offloadedWeight">Offloaded Weight</label>
            <input type="number" min="0" class="form-control" name="offloaded_weight"
                value="{{ old('offloaded_weight', $scheduleFlightCustomerShipment->offloaded_weight ?? 0) }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label" for="totalVolumetricWeight">Total Volumetric Weight</label>
            <input type="number" min="0" class="form-control" name="total_volumetric_weight"
                value="{{ old('total_volumetric_weight', $scheduleFlightCustomerShipment->total_volumetric_weight ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="totalActualWeight">Total Actual Weight</label>
            <input type="number" min="0" class="form-control" name="total_actual_weight"
                value="{{ old('total_actual_weight', $scheduleFlightCustomerShipment->total_actual_weight ?? 0) }}">
        </div>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>

</form>

<!--/ Edit Schedule Flight Cutomer AWB Modal -->