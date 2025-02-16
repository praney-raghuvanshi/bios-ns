<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete the old image if it exists
        if ($user->profile_image) {
            Storage::delete('public/profile_images/' . $user->profile_image);
        }

        // Store the new image
        $imageName = time() . '.' . $request->profile_image->extension();
        $request->profile_image->storeAs('public/profile_images', $imageName);

        // Update the user's profile image path in the database
        $user->profile_image = $imageName;
        $user->save();

        return back()->with('success', 'Profile image updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        if (!Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $request->currentPassword,
        ])) {
            return back()->with('failure', 'Invalid Current Password!');
        }

        $user->password = Hash::make($request->newPassword);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
