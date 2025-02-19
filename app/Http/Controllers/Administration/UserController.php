<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Models\UserFavouriteMenu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $groups = Group::active()->get();
        return view('administration.user.list', compact('users', 'groups'));
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
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'status' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'group' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            $username = $this->generateUsername($request->input('name'));

            User::create([
                'name' => $request->input('name'),
                'username' => $username,
                'email' => $request->input('email'),
                'active' => $request->input('status'),
                'password' => bcrypt($request->input('password')),
                'group_id' => $request->input('group')
            ]);
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.user.list')->with('success', 'User added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $groups = Group::active()->get();
        return view('_partials._modals.user.edit', compact('user', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'status' => ['required'],
            'group' => ['required']
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        try {

            $username = $this->generateUsername($request->input('name'));

            $user->name = $request->input('name');
            $user->username = $username;
            $user->email = $request->input('email');
            $user->active = $request->input('status');
            $user->group_id = $request->input('group');
            $user->save();
        } catch (Exception $e) {
            return back()->with('failure', $e->getMessage());
        }

        return redirect()->route('administration.user.list')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function manageMenuFavourites(Request $request)
    {
        try {

            $menuItemSlug = $request->input('menuItemSlug');
            $menuItemName = $request->input('menuItemName');
            $menuItemUrl = $request->input('menuItemUrl');
            $menuItemIcon = $request->input('menuItemIcon');
            $isFavourite = $request->input('isFavourite');

            $userId = Auth::id();

            if ($isFavourite === 'true') {
                UserFavouriteMenu::where('user_id', $userId)->where('menu_item_slug', $menuItemSlug)->delete();
            } else {
                UserFavouriteMenu::create([
                    'user_id' => $userId,
                    'menu_item_name' => $menuItemName,
                    'menu_item_slug' => $menuItemSlug,
                    'menu_item_link' => $menuItemUrl,
                    'menu_item_icon' => $menuItemIcon
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['success' => true]);
    }

    private function generateUsername($name)
    {
        $nameParts = explode(' ', strtolower($name));

        // If there are at least two words, join the first and second words with a dot
        if (count($nameParts) >= 2) {
            $username = "{$nameParts[0]}.{$nameParts[1]}";
        } else {
            $username = strtolower($nameParts[0]);
        }

        return $username;
    }
}
