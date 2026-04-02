@extends('layouts.doctor')
@section('title', 'Dashboard')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-br from-green-50 to-white border border-green-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-900">Welcome back, Dr. {{ auth()->user()->display_name }} 👋</h2>
        <p class="text-gray-500 text-sm mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>

    @if($nextAppointment)
    {{-- Next Appointment --}}
    <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-2xl shadow-md p-6 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-green-100 text-sm font-medium uppercase tracking-wider mb-1">Next Upcoming Appointment</p>
            <h3 class="text-xl font-bold">{{ $nextAppointment->patient->display_name ?? 'Unknown' }}</h3>
            <p class="text-green-50 mt-1 text-sm">
                {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('D, M d Y') }} • 
                {{ \Carbon\Carbon::parse($nextAppointment->start_time)->format('g:i A') }} – 
                {{ \Carbon\Carbon::parse($nextAppointment->end_time)->format('g:i A') }}
            </p>
            <p class="text-sm text-green-100 mt-1">{{ $nextAppointment->type === 'in_person' ? 'In-Person' : 'Online / Video Call' }}</p>
        </div>
        <a href="{{ route('doctor.appointments.show', $nextAppointment) }}" class="bg-white text-green-700 hover:bg-gray-50 font-medium px-5 py-2.5 rounded-xl transition-colors text-sm text-center shrink-0">
            View Details
        </a>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Patients</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-3xl font-bold text-gray-900">{{ $totalCount }}</p>
            <p class="text-gray-500 text-sm mt-1">Total Appointments</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <p class="text-3xl font-bold text-gray-900">{{ $weekAppointments }}</p>
            <p class="text-gray-500 text-sm mt-1">This Week's Appointments</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col justify-center items-start">
            <span class="bg-green-100 text-green-700 text-2xl font-bold px-3 py-1 rounded-full mb-1">{{ $completionRate }}%</span>
            <p class="text-gray-500 text-sm mt-1">Completion Rate</p>
        </div>
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
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
