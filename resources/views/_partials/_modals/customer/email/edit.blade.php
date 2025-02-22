<!-- Edit Customer Email Modal -->

<form id="editCustomerEmailForm" class="row g-3" method="POST"
    action="{{ route('maintenance.customer.email.update', [$customer, $customerEmail]) }}">
    @csrf
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $customerEmail->name) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="email">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $customerEmail->email) }}" />
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="location">Locations</label>
        <select name="location[]" multiple class="select2ForEdit form-select" data-allow-clear="true">
            <option value="">-- Select Location --</option>
            @foreach ($locations as $location)
            <option value="{{$location->id}}" @if(in_array($location->id, $customerEmailLocationIds)) selected @endif>{{
                $location->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2ForEdit form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $customer->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $customer->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit Customer Email Modal -->