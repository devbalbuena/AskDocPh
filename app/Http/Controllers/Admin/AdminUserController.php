<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /** GET /admin/users — searchable paginated list */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $role   = $request->query('role');

        $users = User::query()
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            }))
            ->when($role, fn($q) => $q->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /** GET /admin/users/{user} — user detail */
    public function show(User $user): View
    {
        $user->load(['doctorApplications', 'appointments', 'crisisReports']);

        return view('admin.users.show', compact('user'));
    }

    /** DELETE /admin/users/{user} — soft delete */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->display_name} has been deactivated.");
    }
}
