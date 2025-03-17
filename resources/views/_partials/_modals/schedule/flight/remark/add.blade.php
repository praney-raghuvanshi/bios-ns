<!-- Add Schedule Flight Remark Modal -->
<div class="modal fade" id="addScheduleFlightRemark" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Remarks</h3>
                </div>
                <form id="addScheduleFlightReamrkForm" class="row g-3" method="POST"
                    action="{{ route('flight-operations.schedule.flight.remark.store', [$schedule, $scheduleFlight]) }}">
                    @csrf

                    <div class="col-12 col-md-12">
                        <label class="form-label" for="customer">Customer</label>
                        <select name="customer[]" class="select2 form-select" multiple data-allow-clear="true" required>
                            <option value="">-- Select Customer --</option>
                            @foreach ($scheduleFlight->scheduleFlightCustomers as $datum)
                            <option value="{{$datum->customer->id}}" @if(old('customer')===$datum->customer->id)
                                selected @endif>{{
                                $datum->customer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-12 d-flex justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="dfr" value="DFR" id="dfr">
                            <label class="form-check-label" for="dfr">DFR</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fpr" value="FPR" id="fpr">
                            <label class="form-check-label" for="fpr">FPR</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email" value="Email" id="email">
                            <label class="form-check-label" for="email">Email</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-12">
                        <label class="form-label" for="remark">Remark</label>
                        <textarea name="remark" class="form-control" rows="3" required>{{ old('remark') }}</textarea>
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
<!--/ Add Schedule Flight Remark Modal -->