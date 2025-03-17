<!-- Add Schedule Flight Customer Product Modal -->
<div class="modal fade" id="addScheduleFlightCustomerProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Product for Customer</h3>
                </div>
                <form id="addScheduleFlightCustomerProductForm" class="row g-3" method="POST"
                    action="{{ route('flight-operations.schedule.flight.customer.product.store', [$schedule, $scheduleFlight, $scheduleFlightCustomer]) }}">
                    @csrf

                    <div class="col-12 col-md-12">
                        <label class="form-label" for="product">Product</label>
                        <select name="product" class="select2 form-select" data-allow-clear="true" required>
                            <option value="">-- Select Product --</option>
                            @foreach ($products as $product)
                            <option value="{{$product->id}}" @if(old('product')===$product->id) selected @endif>{{
                                $product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="upliftedWeight">Uplifted Weight</label>
                        <input type="number" min="0" class="form-control" name="uplifted_weight"
                            value="{{ old('uplifted_weight', 0) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="offloadedWeight">Offloaded Weight</label>
                        <input type="number" min="0" class="form-control" name="offloaded_weight"
                            value="{{ old('offloaded_weight', 0) }}">
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Add Schedule Flight Customer Product Modal -->