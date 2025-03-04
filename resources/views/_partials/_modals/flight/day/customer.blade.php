<!-- Manage Flight Day Customers Modal -->
<div class="modal fade" id="manageCustomersForFlightDay" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Manage Customers for Flight Day</h3>
                </div>
                <form id="manageCustomersForFlightDayForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.flight.day.manage-customers', [$flight, $flightDay]) }}">
                    @csrf
                    <div class="col-12">
                        <select name="customers[]" class="select2 form-select" multiple aria-label="customers">
                            @foreach ($activeCustomers as $customer)
                            <option value="{{$customer->id}}" @if(in_array($customer->id, $flightDayCustomers)) selected
                                @endif>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="update_all_days" class="form-check-input" id="updateAllDays">
                            <label class="form-check-label" for="updateAllDays">Update All Days for this flight</label>
                        </div>
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