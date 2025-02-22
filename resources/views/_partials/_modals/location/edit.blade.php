<!-- Edit Location Modal -->

<form id="editLocationForm" class="row g-3" method="POST"
    action="{{ route('maintenance.location.update', $location) }}">
    @csrf
    <div class="col-12 col-md-6">
        <label class="form-label" for="zone">Zone</label>
        <select name="zone" class="select2 form-select" data-allow-clear="true">
            <option value="">-- Select Zone --</option>
            @foreach ($zones as $zone)
            <option value="{{$zone->id}}" @if(old('zone', $location->zone_id)===$zone->id) selected @endif>{{
                $zone->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="code">Code</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $location->code) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $location->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $location->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $location->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Location Modal -->