@extends('layouts.doctor')
@section('title', 'Dashboard')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-br from-purple-900/40 to-gray-800 border border-green-200 rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-2xl font-bold text-gray-900">Welcome back, Dr. {{ auth()->user()->display_name }} 👋</h2>
        <p class="text-gray-500 text-sm mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
        $stats = [
            ['label' => 'Total Appointments', 'value' => $totalCount,     'color' => 'purple'],
            ['label' => 'Pending Confirmation','value' => $pendingCount,   'color' => 'yellow'],
            ['label' => 'Completed',           'value' => $completedCount,'color' => 'green'],
        ];
        $colors = ['purple' => 'bg-green-100 text-green-600', 'yellow' => 'bg-yellow-100 text-yellow-700', 'green' => 'bg-green-100 text-green-700'];
        @endphp
        @foreach($stats as $stat)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Today's Appointments — {{ now()->format('M d') }}</h3>
            <a href="{{ route('doctor.appointments.index') }}" class="text-green-600 text-xs hover:text-green-700">View all →</a>
        </div>
        @forelse($todayAppointments as $appt)
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200/50 hover:bg-gray-700/20 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-300">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 text-sm font-medium">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-xs">{{ substr($appt->start_time, 0, 5) }} • {{ $appt->type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-3 py-1 rounded-full capitalize">{{ $appt->status }}</span>
                <a href="{{ route('doctor.appointments.show', $appt) }}" class="bg-green-600 hover:bg-green-700 text-gray-900 text-xs px-3 py-1.5 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <p class="text-gray-500 text-sm">No appointments scheduled for today.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
