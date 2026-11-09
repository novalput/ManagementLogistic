<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Borrowing;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $pendingBorrowings = Borrowing::where('status', 'pending')
            ->latest()
            ->get();

        $baseDivisions = collect(['Engineering', 'Mechanical']);
        $dynamicDivisions = \App\Models\User::whereNotNull('division')
            ->distinct()
            ->pluck('division')
            ->filter()
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => Str::lower($v) !== 'other' && !in_array($v, $baseDivisions->all()));
        $divisions = $baseDivisions->merge($dynamicDivisions)->unique()->values();

        return view('profile.edit', [
            'user' => $request->user(),
            'pendingBorrowings' => $pendingBorrowings,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Handle new division logic - prioritize new_division if it exists
        if ($request->filled('new_division') && trim($request->input('new_division')) !== '') {
            $data['division'] = trim($request->input('new_division'));
        }

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar image.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar_path = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-removed');
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
