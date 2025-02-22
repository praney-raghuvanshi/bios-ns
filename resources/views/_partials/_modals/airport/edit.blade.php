<!-- Edit Airport Modal -->

<form id="editAirportForm" class="row g-3" method="POST" action="{{ route('maintenance.airport.update', $airport) }}">
    @csrf
    <div class="col-12 col-md-6">
        <label class="form-label" for="iata">IATA</label>
        <input type="text" name="iata" class="form-control" minlength="3" maxlength="3"
            value="{{ old('iata', $airport->iata) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $airport->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="city">City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $airport->city) }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="country">Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $airport->country) }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="summer_difference">Summer Difference</label>
        <input type="number" name="summer_difference" class="form-control"
            value="{{ old('summer_difference', $airport->summer_difference) }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="winter_difference">Winter Difference</label>
        <input type="number" name="winter_difference" class="form-control"
            value="{{ old('winter_difference', $airport->winter_difference) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $airport->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $airport->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Airport Modal -->