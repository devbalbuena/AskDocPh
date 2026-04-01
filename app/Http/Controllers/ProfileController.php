<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Determine the correct layout for the authenticated user's role.
     */
    private function layout(): string
    {
        return match(auth()->user()->role) {
            'doctor' => 'layouts.doctor',
            'admin'  => 'layouts.admin',
            default  => 'layouts.patient',
        };
    }

    /**
     * Display the user's profile page.
     */
    public function show(): View
    {
        $user   = auth()->user();
        $layout = $this->layout();

        return view('profile.show', compact('user', 'layout'));
    }

    /**
     * Update the user's profile information (text fields + optional file uploads).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'fname'         => ['required', 'string', 'max:100'],
            'mname'         => ['nullable', 'string', 'max:100'],
            'lname'         => ['required', 'string', 'max:100'],
            'username'      => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'bio'           => ['nullable', 'string', 'max:500'],
            'gender'        => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            'bday'          => ['nullable', 'date'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'cover_photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old file if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('profiles', 'public');
        }

        // Handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            if ($user->cover_photo) {
                Storage::disk('public')->delete($user->cover_photo);
            }
            $validated['cover_photo'] = $request->file('cover_photo')
                ->store('profiles/covers', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update only the profile photo (AJAX-friendly endpoint).
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);

        $user = auth()->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->profile_photo = $request->file('profile_photo')
            ->store('profiles', 'public');
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profile photo updated.');
    }
}
