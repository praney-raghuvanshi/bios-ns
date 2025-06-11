<!-- Edit Aircraft Type Modal -->

<form id="editAircraftTypeForm" class="row g-3" method="POST"
    action="{{ route('maintenance.aircraft-type.update', $aircraftType) }}">
    @csrf

    <div class="col-12 col-md-6">
        <label class="form-label" for="aircraft_manufacturer">Aircraft Manufacturer</label>
        <select name="aircraft_manufacturer" class="select2 form-select" data-allow-clear="true">
            <option value="">-- Select Aircraft Manufacturer --</option>
            @foreach ($aircraftManufacturers as $aircraftManufacturer)
            <option value="{{$aircraftManufacturer->id}}" @if(old('aircraft_manufacturer', $aircraftType->
                aircraft_manufacturer_id)===$aircraftManufacturer->id) selected @endif>{{ $aircraftManufacturer->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $aircraftType->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="capacity">Capacity</label>
        <input type="number" name="capacity" class="form-control"
            value="{{ old('capacity', $aircraftType->capacity) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $aircraftType->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $aircraftType->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Aircraft Type Modal -->