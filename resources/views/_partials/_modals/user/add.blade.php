<!-- Add User Modal -->
<div class="modal fade" id="addUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Add User</h3>
                </div>
                <form id="addUserForm" class="row g-3" method="POST" action="{{ route('administration.user.store') }}">
                    @csrf
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe"
                            value="{{ old('name') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@domain.com"
                            value="{{ old('email') }}" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Status --</option>
                            <option value="1" @if(old('status')===1) selected @endif>Active</option>
                            <option value="0" @if(old('status')===0) selected @endif>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="group">Group</label>
                        <select name="group" class="select2 form-select" data-allow-clear="true">
                            <option value="">-- Select Group --</option>
                            @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" />
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
<!--/ Add User Modal -->