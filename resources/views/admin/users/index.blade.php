@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-5">
    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, username..."
               class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-purple-500 w-72">
        <select name="role" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
            <option value="">All Roles</option>
            <option value="patient"  {{ $role === 'patient'  ? 'selected' : '' }}>Patient</option>
            <option value="doctor"   {{ $role === 'doctor'   ? 'selected' : '' }}>Doctor</option>
            <option value="admin"    {{ $role === 'admin'    ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-5 py-2.5 rounded-lg transition-colors">Search</button>
        @if($search || $role)
        <a href="{{ route('admin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white text-sm px-4 py-2.5 rounded-lg transition-colors">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Name</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Role</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Joined</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-600/30 flex items-center justify-center text-xs font-bold text-purple-300">
                                    {{ strtoupper(substr($user->fname, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $user->display_name }}</p>
                                    <p class="text-gray-500 text-xs">@{{ $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-400">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="{{ $user->role === 'doctor' ? 'bg-blue-500/20 text-blue-400' : ($user->role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-gray-600/40 text-gray-300') }} text-xs px-2.5 py-1 rounded-full capitalize">{{ $user->role }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->doctor_status !== 'none' ? ucfirst($user->doctor_status) : '—' }}</td>
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-purple-400 hover:text-purple-300 text-xs transition-colors">View</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Deactivate {{ $user->display_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Deactivate</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-700">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
