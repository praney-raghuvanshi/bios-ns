<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::all();
        return view('administration.group.list', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => ['required', 'string', 'unique:groups,name,NULL,id,deleted_at,NULL']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            Group::create([
                'name' => Str::upper($request->input('group_name')),
                'added_by' => Auth::id()
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.group.list')->with('success', 'Group added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $groupUsers = $group->users;
        $groupRoles = $group->roles->pluck('name')->toArray();
        $availableUsers = User::active()->whereNull('group_id')->get(['id', 'name']);
        $allRoles = Role::pluck('id', 'name')->toArray();
        return view('administration.group.detail', compact('group', 'groupUsers', 'groupRoles', 'availableUsers', 'allRoles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('groups')->ignore($group->id)],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            DB::beginTransaction();

            $newStatus = $request->input('status');

            $group->name = Str::upper($request->input('name'));
            $group->active = $newStatus;
            $group->save();

            // If Group status is Inactive : remove all users & roles from group.
            if ($newStatus == 0) {
                $group->users()->update(['group_id' => null]);
                $group->roles()->detach();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.group.show', $group)->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function manageGroupRoles(Request $request, Group $group)
    {
        try {

            $roleIds = array_filter($request->input('roles', []));

            DB::beginTransaction();

            $oldRoles = $group->roles()->pluck('name');
            $newRoles = Role::whereIn('id', $roleIds)->pluck('name');

            $group->roles()->sync($roleIds);

            activity()->performedOn($group)->by(auth()->user())->useLog('Group')->withProperties([
                'old' => $oldRoles,
                'new' => $newRoles
            ])->log('Group roles updated');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.group.show', $group)->with('success', 'Group roles updated successfully.');
    }

    public function manageGroupUsers(Request $request, Group $group)
    {
        try {

            // Get the selected user IDs from the form
            $newUserIds = $request->input('users', []);

            // Get the current users of the group
            $currentUserIds = $group->users->pluck('id')->toArray();

            // Determine users to add and remove
            $usersToAdd = array_diff($newUserIds, $currentUserIds);
            $usersToRemove = array_diff($currentUserIds, $newUserIds);

            DB::beginTransaction();

            // Update group users
            User::whereIn('id', $usersToRemove)->update(['group_id' => null]); // Remove group association
            User::whereIn('id', $usersToAdd)->update(['group_id' => $group->id]); // Assign group association

            activity()->performedOn($group)->by(auth()->user())->useLog('Group')->withProperties([
                'old' => $currentUserIds,
                'new' => $newUserIds
            ])->log('Group users updated');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.group.show', $group)->with('success', 'Group users updated successfully.');
    }
}
