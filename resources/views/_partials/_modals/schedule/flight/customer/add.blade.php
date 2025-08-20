<!-- Add Schedule Flight Customer Modal -->
<div class="modal fade" id="addScheduleFlightCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Customer to Schedule Flight</h3>
                </div>
                <form id="addScheduleFlightCustomerForm" class="row g-3" method="POST"
                    action="{{ route('flight-operations.schedule.flight.customer.store', [$schedule, $scheduleFlight]) }}">
                    @csrf

                    <div class="col-12 col-md-12">
                        <label class="form-label" for="customer">Customer</label>
                        <select name="customer" class="select2 form-select" data-allow-clear="true" required>
                            <option value="">-- Select Customer --</option>
                            @foreach ($customers as $customer)
                            <option value="{{$customer->id}}" @if(old('customer')===$customer->id) selected @endif>{{
                                $customer->formatted_name }}
                            </option>
                            @endforeach
                        </select>
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
<!--/ Add Schedule Flight Customer Modal -->