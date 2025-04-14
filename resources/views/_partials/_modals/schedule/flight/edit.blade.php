<!-- Edit Schedule Flight Modal -->
<div class="modal fade" id="editScheduleFlight" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Schedule Flight</h3>
                </div>
                <form id="editScheduleFlightForm" class="row g-3" method="POST"
                    action="{{ route('flight-operations.schedule.flight.update', [$schedule, $scheduleFlight]) }}">
                    @csrf

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="std">STD</label>
                        <input type="time" name="std" class="form-control time24"
                            value="{{ old('std', $scheduleFlight->flight->departure_time) }}" disabled />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="sta">STA</label>
                        <input type="time" name="sta" class="form-control time24"
                            value="{{ old('sta', $scheduleFlight->flight->arrival_time) }}" disabled />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="etd">ETD</label>
                        <input type="time" name="etd" class="form-control time24"
                            value="{{ old('etd', $scheduleFlight->estimated_departure_time) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="eta">ETA</label>
                        <input type="time" name="eta" class="form-control time24"
                            value="{{ old('eta', $scheduleFlight->estimated_arrival_time) }}" />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="atd">ATD</label>
                        <input type="time" name="atd" class="form-control time24"
                            value="{{ old('atd', $scheduleFlight->actual_departure_time) }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="ata">ATA</label>
                        <input type="time" name="ata" class="form-control time24"
                            value="{{ old('ata', $scheduleFlight->actual_arrival_time) }}" />
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
<!--/ Edit Schedule Flight Modal -->