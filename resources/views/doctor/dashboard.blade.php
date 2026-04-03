@extends('layouts.doctor')
@section('title', 'Dashboard')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-3xl shadow-md p-8 relative overflow-hidden mb-6">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-10 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full bg-white"></div>
        </div>
        <div class="relative z-10 text-white">
            <h2 class="text-3xl font-bold text-white">Welcome back, Dr. {{ auth()->user()->display_name }} 👋</h2>
            <p class="text-green-100 text-sm mt-2">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>

    @if($nextAppointment)
    {{-- Next Appointment --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-1">Next Upcoming Appointment</p>
            <h3 class="text-xl font-bold text-gray-900">{{ $nextAppointment->patient->display_name ?? 'Unknown' }}</h3>
            <p class="text-gray-700 mt-1 text-sm">
                {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('D, M d Y') }} • 
                {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('g:i A') }} – 
                {{ \Carbon\Carbon::parse($nextAppointment->end_time)->format('g:i A') }}
            </p>
            <p class="text-sm text-green-600 font-semibold mt-1">{{ $nextAppointment->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }}</p>
        </div>
        <a href="{{ route('doctor.appointments.show', $nextAppointment) }}" class="bg-green-600 text-white hover:bg-green-700 font-medium px-5 py-2.5 rounded-xl transition-colors text-sm text-center shrink-0">
            View Details
        </a>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Patients</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $totalCount }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Appointments</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $weekAppointments }}</p>
            <p class="text-gray-500 text-sm mt-1">This Week's Appointments</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-start">
            <span class="bg-green-100 text-green-700 text-2xl font-bold px-3 py-1 rounded-full mb-1">{{ $completionRate }}%</span>
            <p class="text-gray-500 text-sm mt-1">Completion Rate</p>
        </div>
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Today's Appointments — {{ now()->format('M d') }}</h3>
            <a href="{{ route('doctor.appointments.index') }}" class="text-green-600 text-xs hover:text-green-700">View all →</a>
        </div>
        @forelse($todayAppointments as $appt)
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200/50 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-600">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 text-sm font-medium">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($appt->start_time)->format('g:i A') }} • {{ $appt->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="{{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-3 py-1 rounded-full capitalize">{{ $appt->status }}</span>
                <a href="{{ route('doctor.appointments.show', $appt) }}" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">View</a>
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
