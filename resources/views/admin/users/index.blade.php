@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="space-y-5">
    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search users by name, email or username..."
               class="bg-white border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:border-green-500 w-72">
        <select name="role" class="bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-green-500">
            <option value="">All Roles</option>
            <option value="patient"  {{ $role === 'patient'  ? 'selected' : '' }}>Patient</option>
            <option value="doctor"   {{ $role === 'doctor'   ? 'selected' : '' }}>Doctor</option>
            <option value="admin"    {{ $role === 'admin'    ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-5 py-2.5 rounded-lg transition-colors">Search</button>
        @if($search || $role)
        <a href="{{ route('admin.users.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-900 text-sm px-4 py-2.5 rounded-lg transition-colors">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Name</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Role</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Joined</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $member)
                    <tr class="border-b border-gray-200/50 hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-700">
                                    {{ strtoupper(substr($member->fname, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-gray-900 font-medium">{{ $member->display_name }}</p>
                                    <p class="text-gray-500 text-xs">&commat;{{ $member->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $member->email }}</td>
                        <td class="px-5 py-3">
                            <span class="{{ $member->role === 'doctor' ? 'bg-blue-100 text-blue-700' : ($member->role === 'admin' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600') }} text-xs px-2.5 py-1 rounded-full capitalize">{{ $member->role }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($member->trashed())
                                <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full">Inactive</span>
                            @elseif($member->role === 'doctor')
                                @php
                                    $dsColor = match($member->doctor_status) {
                                        'approved'  => 'bg-green-100 text-green-700',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'rejected'  => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="{{ $dsColor }} text-xs px-2.5 py-1 rounded-full capitalize">{{ ucfirst($member->doctor_status) }}</span>
                            @else
                                <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full">Active</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $member->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $member) }}" class="text-green-600 hover:text-green-700 text-xs transition-colors">View</a>
                                @if($member->id !== auth()->id() && !$member->trashed())
                                <form method="POST" action="{{ route('admin.users.destroy', $member) }}" onsubmit="return confirm('Deactivate {{ $member->display_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Deactivate</button>
                                </form>
                                @endif
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
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
