<!-- Manage Group User(s) Modal -->
<div class="modal fade" id="manageUsersForGroup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-group-user">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Manage Users for Group : <strong>{{ $group->name }}</strong></h3>
                </div>
                <form id="manageGroupUsersForm" class="row g-3" method="POST"
                    action="{{ route('administration.group.manage-users', $group) }}">
                    @csrf
                    <div class="col-12">
                        <select name="users[]" class="select2 form-select" multiple aria-label="users">
                            @foreach ($groupUsers as $groupUser)
                            <option value="{{$groupUser->id}}" selected>{{ $groupUser->name }}</option>
                            @endforeach
                            @foreach ($availableUsers as $user)
                            <option value="{{$user->id}}">{{ $user->name }}</option>
                            @endforeach
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