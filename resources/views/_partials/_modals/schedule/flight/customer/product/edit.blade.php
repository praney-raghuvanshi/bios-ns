<!-- Edit Schedule Flight Cutomer Product Modal -->

<form id="editScheduleFlightCutomerProductForm" class="row g-3" method="POST"
    action="{{ route('flight-operations.schedule.flight.customer.product.update', [$schedule, $scheduleFlight, $scheduleFlightCustomer, $scheduleFlightCustomerProduct]) }}">
    @csrf

    <div class="col-12 col-md-12">
        <label class="form-label" for="product">Product</label>
        <select name="product" class="select2 form-select" data-allow-clear="true" disabled>
            <option value="">-- Select Product --</option>
            @foreach ($products as $product)
            <option value="{{$product->id}}" @if(old('product', $scheduleFlightCustomerProduct->
                product_id)===$product->id) selected @endif>{{
                $product->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="upliftedWeight">Uplifted Weight</label>
        <input type="number" min="0" class="form-control" name="uplifted_weight"
            value="{{ old('uplifted_weight', $scheduleFlightCustomerProduct->uplifted_weight ?? 0) }}">
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="offloadedWeight">Offloaded Weight</label>
        <input type="number" min="0" class="form-control" name="offloaded_weight"
            value="{{ old('offloaded_weight', $scheduleFlightCustomerProduct->offloaded_weight ?? 0) }}">
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Schedule Flight Cutomer Product Modal -->