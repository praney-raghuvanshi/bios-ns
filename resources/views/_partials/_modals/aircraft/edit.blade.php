<!-- Edit Aircraft Modal -->

<form id="editAircraftForm" class="row g-3" method="POST"
    action="{{ route('maintenance.aircraft.update', $aircraft) }}">
    @csrf

    <div class="col-12 col-md-6">
        <label class="form-label" for="aircraft_type">Aircraft Type</label>
        <select name="aircraft_type" class="select2 form-select" data-allow-clear="true">
            <option value="">-- Select Aircraft Type --</option>
            @foreach ($aircraftTypes as $aircraftType)
            <option value="{{$aircraftType->id}}" @if(old('aircraft_type', $aircraft->
                aircraft_type_id)===$aircraftType->id) selected @endif>{{
                $aircraftType->formatted_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="registration">Registration</label>
        <input type="text" name="registration" class="form-control"
            value="{{ old('registration', $aircraft->registration) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $aircraft->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $aircraft->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Aircraft Modal -->