<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'mname' => 'nullable|string|max:50',
            'username' => 'required|string|max:30|unique:users',
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'gender' => 'nullable|string',
            'bday' => 'nullable|date',
            'role' => 'nullable|in:patient,doctor',
        ]);

        $user = User::create([
            'fname'    => $request->fname,
            'lname'    => $request->lname,
            'mname'    => $request->mname,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'gender'   => $request->gender,
            'bday'     => $request->bday,
            'role'     => $request->role ?? 'patient',
            'doctor_status' => $request->role === 'doctor' ? 'pending' : 'none',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return match($user->role) {
            'doctor' => redirect('/doctor/dashboard'),
            default => redirect('/patient/dashboard'),
        };
    }
}
