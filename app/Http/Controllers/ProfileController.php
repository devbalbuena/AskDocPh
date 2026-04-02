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

        // For doctors, decode the bio column (JSON) into structured professional fields.
        // For all other roles, $professional is an empty array (bio is plain text).
        $professional = [];
        if ($user->role === 'doctor' && $user->bio) {
            $decoded = json_decode($user->bio, true);
            if (is_array($decoded)) {
                $professional = $decoded;
            }
        }

        return view('profile.show', compact('user', 'layout', 'professional'));
    }

    /**
     * Update the user's profile information (text fields + optional file uploads).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $rules = [
            'fname'         => ['required', 'string', 'max:100'],
            'mname'         => ['nullable', 'string', 'max:100'],
            'lname'         => ['required', 'string', 'max:100'],
            'username'      => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'bio'           => ['nullable', 'string', 'max:500'],
            'gender'        => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            'bday'          => ['nullable', 'date'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'cover_photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ];

        // Extra validation for doctor professional fields
        if ($user->role === 'doctor') {
            $rules['specialization']    = ['nullable', 'string', 'max:255'];
            $rules['prc_license_number'] = ['nullable', 'string', 'max:100'];
            $rules['hospital_affiliation'] = ['nullable', 'string', 'max:255'];
            $rules['years_experience']  = ['nullable', 'integer', 'min:0', 'max:60'];
        }

        $validated = $request->validate($rules);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
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

        // For doctors: encode professional fields as JSON into the bio column.
        // Remove individual professional keys from $validated so they don't
        // get written to non-existent columns, then set bio to JSON.
        if ($user->role === 'doctor') {
            $validated['bio'] = json_encode([
                'specialization'    => $request->input('specialization'),
                'prc_license'       => $request->input('prc_license_number'),
                'hospital'          => $request->input('hospital_affiliation'),
                'years_experience'  => $request->input('years_experience'),
            ]);
            // Remove doctor-only keys that aren't real columns
            unset(
                $validated['specialization'],
                $validated['prc_license_number'],
                $validated['hospital_affiliation'],
                $validated['years_experience']
            );
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
