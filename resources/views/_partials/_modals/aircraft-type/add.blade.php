<!-- Add Aircraft Type Modal -->
<div class="modal fade" id="addAircraftType" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Aircraft Type</h3>
                </div>
                <form id="addAircraftTypeForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.aircraft-type.store') }}">
                    @csrf

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="aircraft_manufacturer">Aircraft Manufacturer</label>
                        <select name="aircraft_manufacturer" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Aircraft Manufacturer --</option>
                            @foreach ($aircraftManufacturers as $aircraftManufacturer)
                            <option value="{{$aircraftManufacturer->id}}"
                                @if(old('aircraft_manufacturer')===$aircraftManufacturer->id) selected
                                @endif>{{ $aircraftManufacturer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" />
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="capacity">Capacity</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" />
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
<!--/ Add Aircraft Type Modal -->