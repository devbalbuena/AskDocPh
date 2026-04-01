@extends('layouts.admin')
@section('title', 'User — ' . $user->display_name)
@section('page-title', 'User Detail')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Users
    </a>

    {{-- User Profile Card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Cover --}}
        <div class="h-28 bg-gradient-to-br from-green-700 via-green-800 to-emerald-900 relative overflow-hidden">
            @if($user->cover_photo)
                <img src="{{ Storage::url($user->cover_photo) }}" class="w-full h-full object-cover" alt="">
            @endif
        </div>

        <div class="px-6 pb-6 relative">
            {{-- Profile photo --}}
            <div class="absolute -top-10 left-6">
                <div class="w-20 h-20 rounded-full border-4 border-white shadow-lg overflow-hidden bg-green-100">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}" class="w-full h-full object-cover" alt="{{ $user->display_name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl font-bold text-green-700">
                            {{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-12">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->display_name }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">@{{ $user->username }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $user->email }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        {{-- Role badge --}}
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full capitalize
                            {{ match($user->role) {
                                'doctor' => 'bg-blue-100 text-blue-700',
                                'admin'  => 'bg-green-100 text-green-700',
                                default  => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ $user->role }}
                        </span>
                        {{-- Doctor status badge --}}
                        @if($user->doctor_status && $user->doctor_status !== 'none')
                        <span class="text-xs font-semibold px-3 py-1 rounded-full capitalize
                            {{ match($user->doctor_status) {
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'rejected' => 'bg-red-100 text-red-600',
                                default    => 'bg-gray-100 text-gray-500',
                            } }}">
                            Dr. {{ ucfirst($user->doctor_status) }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Bio --}}
                @if($user->bio)
                <p class="text-sm text-gray-700 mt-3 leading-relaxed">{{ $user->bio }}</p>
                @endif

                {{-- Meta info --}}
                <div class="mt-4 grid grid-cols-2 gap-3">
                    @if($user->gender)
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Gender</p>
                        <p class="text-sm text-gray-800 font-semibold mt-0.5 capitalize">{{ str_replace('_', ' ', $user->gender) }}</p>
                    </div>
                    @endif
                    @if($user->bday)
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Birthday</p>
                        <p class="text-sm text-gray-800 font-semibold mt-0.5">{{ $user->bday->format('F j, Y') }}</p>
                    </div>
                    @endif
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Joined</p>
                        <p class="text-sm text-gray-800 font-semibold mt-0.5">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl px-4 py-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Appointments</p>
                        <p class="text-sm text-gray-800 font-semibold mt-0.5">{{ $user->appointments->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Doctor Applications --}}
    @if($user->doctorApplications->count())
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Doctor Applications</h3>
        <div class="space-y-3">
            @foreach($user->doctorApplications as $app)
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                <div>
                    <p class="text-sm text-gray-800">Submitted {{ $app->submitted_at?->format('M d, Y') ?? 'N/A' }}</p>
                    @if($app->admin_notes)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $app->admin_notes }}</p>
                    @endif
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize
                    {{ match($app->status) {
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'pending'  => 'bg-yellow-100 text-yellow-700',
                        'rejected' => 'bg-red-100 text-red-600',
                        default    => 'bg-gray-200 text-gray-600',
                    } }}">
                    {{ $app->status }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Crisis Reports --}}
    @if($user->crisisReports->count())
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Crisis Reports <span class="text-red-500">({{ $user->crisisReports->count() }})</span></h3>
        <div class="space-y-3">
            @foreach($user->crisisReports->take(3) as $report)
            <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-red-600 capitalize">{{ $report->severity_level ?? 'Unknown severity' }}</span>
                    <span class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</span>
                </div>
                @if($report->description)
                <p class="text-sm text-gray-700 mt-1 line-clamp-2">{{ $report->description }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Danger Zone —Deactivate --}}
    @unless($user->id === auth()->id())
    <div class="bg-white border border-red-200 rounded-2xl shadow-sm p-5">
        <h3 class="text-sm font-semibold text-red-600 mb-1">Danger Zone</h3>
        <p class="text-xs text-gray-500 mb-4">Deactivating this account will soft-delete the user. They will no longer be able to log in.</p>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              onsubmit="return confirm('Are you sure you want to deactivate {{ addslashes($user->display_name) }}? This action can be reversed from the database.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors">
                Deactivate Account
            </button>
        </form>
    </div>
    @endunless
</div>
@endsection
