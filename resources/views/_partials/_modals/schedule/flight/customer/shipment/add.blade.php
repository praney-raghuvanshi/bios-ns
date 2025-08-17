<!-- Add Schedule Flight Customer Shipment Modal -->
<div class="modal fade" id="addScheduleFlightCustomerAwb" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add AWB for Customer</h3>
                </div>

                <form id="addScheduleFlightCustomerAwbForm" class="row g-3" method="POST"
                    action="{{ route('flight-operations.schedule.flight.customer.shipment.store', [$schedule, $scheduleFlight, $scheduleFlightCustomer]) }}">
                    @csrf

                    <!-- Row 1: Radio Buttons -->
                    <div class="d-flex justify-content-center mb-3">
                        <label class="form-label me-3">Select AWB Type:</label>
                        <div class="form-check me-3">
                            <input class="form-check-input" type="radio" name="awb_type" id="newAwbRadio" value="new">
                            <label class="form-check-label" for="newAwbRadio">New/Connecting AWB</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="awb_type" id="subsequentAwbRadio"
                                value="subsequent">
                            <label class="form-check-label" for="subsequentAwbRadio">Subsequent AWB</label>
                        </div>
                    </div>

                    <!-- Row 2: AWB Inputs with Lookup Buttons -->
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label for="newAwb" class="form-label">New/Connecting AWB</label>
                            <input type="number" class="form-control" id="newAwb" name="new_awb" disabled>
                            <button type="button" class="btn btn-info check-awb mt-1" data-type="new">Check</button>
                        </div>
                        <div class="col-md-6">
                            <label for="subsequentAwb" class="form-label">Subsequent AWB</label>
                            <input type="number" class="form-control" id="subsequentAwb" name="subsequent_awb" disabled>
                            <button type="button" class="btn btn-info check-awb mt-1"
                                data-type="subsequent">Lookup</button>
                        </div>
                    </div>

                    <!-- AWB Message -->
                    <div class="mb-1 mx-1">
                        <div id="awbMessage" class="text-danger"></div>
                    </div>

                    <!-- Row 3: Product + Destination -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="product">Product</label>
                            <select name="product" class="select2 form-select" id="product" disabled>
                                <option value="">-- Select Product --</option>
                                @foreach ($scheduleFlightCustomer->scheduleFlightCustomerProducts as $item)
                                <option value="{{$item->product_id}}" @if(old('product')===$item->product_id) selected
                                    @endif>
                                    {{ $item->product->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="destination">Destination</label>
                            <select name="destination" class="select2 form-select" id="destination" disabled>
                                <option value="">-- Select Destination --</option>
                                @foreach ($airports as $item)
                                <option value="{{$item->id}}" @if(old('destination')===$item->id) selected @endif>
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Weights -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="declaredWeight">Declared Weight</label>
                            <input type="number" min="0" class="form-control" id="declaredWeight" name="declared_weight"
                                value="{{ old('declared_weight') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="actualWeight">Actual Weight</label>
                            <input type="number" min="0" class="form-control" id="actualWeight" name="actual_weight"
                                value="{{ old('actual_weight') }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="volumetricWeight">Volumetric Weight</label>
                            <input type="number" min="0" class="form-control" id="volumetricWeight"
                                name="volumetric_weight" value="{{ old('volumetric_weight') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="offloadedWeight">Offloaded Weight</label>
                            <input type="number" min="0" class="form-control" id="offloadedWeight"
                                name="offloaded_weight" value="{{ old('offloaded_weight') }}" disabled>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="totalVolumetricWeight">Total Volumetric Weight</label>
                            <input type="number" min="0" class="form-control" id="totalVolumetricWeight"
                                name="total_volumetric_weight" value="{{ old('total_volumetric_weight') }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="totalActualWeight">Total Actual Weight</label>
                            <input type="number" min="0" class="form-control" id="totalActualWeight"
                                name="total_actual_weight" value="{{ old('total_actual_weight') }}" disabled>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Add Schedule Flight Customer Shipment Modal -->