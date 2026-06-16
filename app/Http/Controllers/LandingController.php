<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return match (auth()->user()->role) {
                'admin' => redirect('/admin/dashboard'),
                'doctor' => redirect('/doctor/dashboard'),
                default => redirect('/patient/dashboard'),
            };
        }
        return view('landing');
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        return back()->with('contact_success', true);
    }
}
