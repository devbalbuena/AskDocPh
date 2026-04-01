@extends('layouts.doctor')
@section('title', 'Dashboard')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-br from-purple-900/40 to-gray-800 border border-purple-700/30 rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-white">Welcome back, Dr. {{ auth()->user()->display_name }} 👋</h2>
        <p class="text-gray-400 text-sm mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
        $stats = [
            ['label' => 'Total Appointments', 'value' => $totalCount,     'color' => 'purple'],
            ['label' => 'Pending Confirmation','value' => $pendingCount,   'color' => 'yellow'],
            ['label' => 'Completed',           'value' => $completedCount,'color' => 'green'],
        ];
        $colors = ['purple' => 'bg-purple-500/20 text-purple-400', 'yellow' => 'bg-yellow-500/20 text-yellow-400', 'green' => 'bg-green-500/20 text-green-400'];
        @endphp
        @foreach($stats as $stat)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5">
            <p class="text-3xl font-bold text-white">{{ $stat['value'] }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
            <h3 class="font-semibold text-white">Today's Appointments — {{ now()->format('M d') }}</h3>
            <a href="{{ route('doctor.appointments.index') }}" class="text-purple-400 text-xs hover:text-purple-300">View all →</a>
        </div>
        @forelse($todayAppointments as $appt)
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700/50 hover:bg-gray-700/20 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-600/30 flex items-center justify-center text-sm font-bold text-blue-300">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-medium">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-xs">{{ substr($appt->start_time, 0, 5) }} • {{ $appt->type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }} text-xs px-3 py-1 rounded-full capitalize">{{ $appt->status }}</span>
                <a href="{{ route('doctor.appointments.show', $appt) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <p class="text-gray-400 text-sm">No appointments scheduled for today.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
