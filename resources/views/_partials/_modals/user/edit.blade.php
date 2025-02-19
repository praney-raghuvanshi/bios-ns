<!-- Edit User Modal -->

<form id="editUserForm" class="row g-3" method="POST" action="{{ route('administration.user.update', $user) }}">
    @csrf
    <div class="col-12 col-md-6">
        <label class="form-label" for="name">Name</label>
        <input type="text" name="name" class="form-control" placeholder="John Doe"
            value="{{ old('name', $user->name) }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="email">Email</label>
        <input type="email" name="email" class="form-control" placeholder="example@domain.com"
            value="{{ old('email', $user->email) }}" />
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="status">Status</label>
        <select name="status" class="select2 form-select" aria-label="status">
            <option value="">Status</option>
            <option value="1" @if(old('status', $user->active) === 1) selected @endif>Active</option>
            <option value="0" @if(old('status', $user->active) === 0) selected @endif>Inactive</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="group">Group</label>
        <select name="group" class="select2 form-select" data-allow-clear="true">
            <option value="">-- Select Group --</option>
            @foreach ($groups as $group)
            <option value="{{ $group->id }}" @if(old('group', $user->group_id) === $group->id) selected @endif>{{
                $group->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
    </div>
</form>

<!--/ Edit User Modal -->