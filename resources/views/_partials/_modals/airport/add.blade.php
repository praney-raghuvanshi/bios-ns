<!-- Add Airport Modal -->
<div class="modal fade" id="addAirport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add Airport</h3>
                </div>
                <form id="addAirportForm" class="row g-3" method="POST"
                    action="{{ route('maintenance.airport.store') }}">
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="iata">IATA</label>
                        <input type="text" name="iata" class="form-control" minlength="3" maxlength="3"
                            value="{{ old('iata') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="city">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="country">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="timezone">Timezone</label>
                        <select name="timezone" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Timezone --</option>
                            @foreach ($timezones as $tz)
                            <option value="{{$tz}}" @if(old('timezone')===$tz) selected @endif>{{$tz}}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-12 col-md-6">
                        <label class="form-label" for="summer_difference">Summer Difference</label>
                        <input type="number" name="summer_difference" class="form-control"
                            value="{{ old('summer_difference') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="winter_difference">Winter Difference</label>
                        <input type="number" name="winter_difference" class="form-control"
                            value="{{ old('winter_difference') }}" />
                    </div> --}}
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
<!--/ Add Airport Modal -->