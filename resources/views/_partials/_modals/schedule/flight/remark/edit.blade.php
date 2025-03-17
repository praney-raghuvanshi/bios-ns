<!-- Edit Schedule Flight Modal -->

<form id="editScheduleFlightRemarkForm" class="row g-3" method="POST"
    action="{{ route('flight-operations.schedule.flight.remark.update', [$schedule, $scheduleFlight, $scheduleFlightRemark]) }}">
    @csrf

    @if(!$scheduleFlightRemark->is_dfr)
    <div class="col-12 col-md-12">
        <label class="form-label" for="customer">Customer</label>
        <select name="customer[]" class="select2ForEdit form-select" multiple data-allow-clear="true" disabled>
            <option value="">-- Select Customer --</option>
            @foreach ($scheduleFlight->scheduleFlightCustomers as $datum)
            <option value="{{$datum->customer->id}}" @if(old('customer', $scheduleFlightRemark->
                customer_id)===$datum->customer->id)
                selected @endif>{{
                $datum->customer->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-12 d-flex justify-content-around">
        {{-- <div class="form-check">
            <input class="form-check-input" type="checkbox" name="dfr" value="DFR" id="dfr"
                @if($scheduleFlightRemark->is_dfr) checked @endif>
            <label class="form-check-label" for="dfr">DFR</label>
        </div> --}}
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="fpr" value="FPR" id="fpr"
                @if($scheduleFlightRemark->is_fpr) checked @endif>
            <label class="form-check-label" for="fpr">FPR</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="email" value="Email" id="email"
                @if($scheduleFlightRemark->email_required) checked @endif disabled>
            <label class="form-check-label" for="email">Email</label>
        </div>
    </div>
    @endif

    <div class="col-12 col-md-12">
        <label class="form-label" for="remark">Remark</label>
        <textarea name="remark" class="form-control" rows="3"
            required>{{ old('remark', $scheduleFlightRemark->remark) }}</textarea>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Schedule Flight Remark Modal -->