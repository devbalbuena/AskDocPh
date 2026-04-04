<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

        $professional = [];
        $emergency = [];

        if ($user->bio) {
            $decoded = json_decode($user->bio, true);
            if (is_array($decoded)) {
                if ($user->role === 'doctor') {
                    $professional = $decoded;
                } elseif ($user->role === 'patient') {
                    $emergency = $decoded;
                }
            }
        }

        return view('profile.show', compact('user', 'layout', 'professional', 'emergency'));
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

        // Extra validation for role specific fields
        if ($user->role === 'doctor') {
            $rules['specialization']    = ['nullable', 'string', 'max:255'];
            $rules['prc_license_number'] = ['nullable', 'string', 'max:100'];
            $rules['hospital_affiliation'] = ['nullable', 'string', 'max:255'];
            $rules['years_experience']  = ['nullable', 'integer', 'min:0', 'max:60'];
        } elseif ($user->role === 'patient') {
            $rules['emergency_contact_name'] = ['nullable', 'string', 'max:255'];
            $rules['emergency_contact_number'] = ['nullable', 'string', 'max:255'];
            $rules['emergency_contact_relationship'] = ['nullable', 'string', 'max:255'];
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

        // Encode professional or emergency fields as JSON into the bio column.
        if ($user->role === 'doctor') {
            $validated['bio'] = json_encode([
                'specialization'    => $request->input('specialization'),
                'prc_license'       => $request->input('prc_license_number'),
                'hospital'          => $request->input('hospital_affiliation'),
                'years_experience'  => $request->input('years_experience'),
            ]);
            unset(
                $validated['specialization'],
                $validated['prc_license_number'],
                $validated['hospital_affiliation'],
                $validated['years_experience']
            );
        } elseif ($user->role === 'patient') {
            $validated['bio'] = json_encode([
                'emergency_name'    => $request->input('emergency_contact_name'),
                'emergency_number'  => $request->input('emergency_contact_number'),
                'emergency_relationship' => $request->input('emergency_contact_relationship'),
            ]);
            unset(
                $validated['emergency_contact_name'],
                $validated['emergency_contact_number'],
                $validated['emergency_contact_relationship']
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

    /**
     * Toggle Two-Factor Authentication.
     */
    public function toggle2FA(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isDemo()) {
            return response()->json(['error' => 'Demo accounts cannot toggle 2FA.'], 403);
        }

        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->save();

        return response()->json([
            'success' => true,
            'enabled' => $user->two_factor_enabled
        ]);
    }
}
