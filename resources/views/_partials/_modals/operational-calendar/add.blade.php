<!-- Add Operational Calendar Modal -->
<div class="modal fade" id="addOperationalCalendar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Operational Calendar</h3>
                </div>
                <form id="addOperationalCalendarForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.operational-calendar.store') }}">
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="year">Year</label>
                        <input type="number" name="year" min="2025" step="1" max="2100" class="form-control"
                            value="{{ old('year') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="weeks">Weeks</label>
                        <input type="number" name="weeks" min="1" step="1" max="52" class="form-control"
                            value="{{ old('weeks') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Status --</option>
                            <option value="1" @if(old('status')===1) selected @endif>Active</option>
                            <option value="0" @if(old('status')===0) selected @endif>Inactive</option>
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
<!--/ Add Operational Calendar Modal -->