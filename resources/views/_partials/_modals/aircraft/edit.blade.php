<!-- Edit Aircraft Modal -->

<form id="editAircraftForm" class="row g-3" method="POST"
    action="{{ route('maintenance.aircraft.update', $aircraft) }}">
    @csrf

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $aircraft->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="capacity">Capacity</label>
        <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $aircraft->capacity) }}" />
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