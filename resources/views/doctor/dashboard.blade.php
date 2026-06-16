@extends('layouts.doctor')
@section('title', 'Dashboard')
@section('page-title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">

    @if(!auth()->user()->isVerifiedDoctor() && !auth()->user()->isDemo())
    {{-- Status Tracker --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 md:p-8 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-6">Verification Progress</h3>
        
        @php
            $user = auth()->user();
            
            $profInfo = [];
            if ($user->bio) {
                $decoded = json_decode($user->bio, true);
                if (is_array($decoded)) {
                    $profInfo = $decoded;
                }
            }
            
            // Define conditions
            $step1 = true; // Always registered
            $step2 = !empty($profInfo['specialization']) && !empty($profInfo['prc_license']); // Profile completed
            $step3 = $step2; // For AskDocPH, once profile is complete, they are considered under review if pending
            $step4 = $user->doctor_status === 'approved';
        @endphp

        <div class="relative">
            {{-- Connective Line --}}
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 md:hidden"></div>
            <div class="hidden md:block absolute top-4 left-0 right-0 h-0.5 bg-gray-200" style="left: 12.5%; right: 12.5%;"></div>

            <div class="flex flex-col md:flex-row justify-between gap-6 relative z-10">
                
                {{-- Step 1 --}}
                <div class="flex md:flex-col items-start md:items-center gap-4 md:gap-2 relative w-full md:w-1/4">
                    <div class="hidden md:block absolute top-4 right-1/2 w-1/2 h-0.5 bg-green-500"></div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border-2 {{ $step1 ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} transition-colors relative z-10">
                        @if($step1)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="text-sm font-semibold">1</span>
                        @endif
                    </div>
                    <div class="md:text-center mt-1 md:mt-0">
                        <p class="text-sm font-bold {{ $step1 ? 'text-gray-900' : 'text-gray-500' }}">Registered</p>
                        <p class="text-xs text-gray-500 mt-0.5">Account created successfully.</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex md:flex-col items-start md:items-center gap-4 md:gap-2 relative w-full md:w-1/4">
                    <div class="hidden md:block absolute top-4 left-0 w-1/2 h-0.5 {{ $step1 ? 'bg-green-500' : 'bg-gray-200' }} -z-10"></div>
                    <div class="hidden md:block absolute top-4 right-1/2 w-1/2 h-0.5 {{ $step2 ? 'bg-green-500' : 'bg-gray-200' }} -z-10"></div>
                    
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border-2 {{ $step2 ? 'bg-green-500 border-green-500 text-white' : ($step1 ? 'bg-green-50 border-green-500 text-green-600' : 'bg-white border-gray-300 text-gray-400') }} transition-colors relative z-10">
                        @if($step2)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($step1)
                            <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                        @else
                            <span class="text-sm font-semibold">2</span>
                        @endif
                    </div>
                    <div class="md:text-center mt-1 md:mt-0">
                        <p class="text-sm font-bold {{ $step2 || $step1 ? 'text-gray-900' : 'text-gray-500' }}">Complete Profile</p>
                        <p class="text-xs mt-0.5 {{ $step2 ? 'text-gray-500' : 'text-gray-600 font-medium' }}">
                            @if($step2)
                                Professional details added.
                            @else
                                <a href="{{ route('profile.edit') }}" class="text-green-600 hover:text-green-700 underline">Add PRC License & Details →</a>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex md:flex-col items-start md:items-center gap-4 md:gap-2 relative w-full md:w-1/4">
                    <div class="hidden md:block absolute top-4 left-0 w-1/2 h-0.5 {{ $step2 ? 'bg-green-500' : 'bg-gray-200' }} -z-10"></div>
                    <div class="hidden md:block absolute top-4 right-1/2 w-1/2 h-0.5 {{ $step3 ? 'bg-green-500' : 'bg-gray-200' }} -z-10"></div>
                    
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border-2 {{ $step4 ? 'bg-green-500 border-green-500 text-white' : ($step3 && !$step4 ? 'bg-yellow-100 border-yellow-500 text-yellow-600' : 'bg-white border-gray-300 text-gray-400') }} transition-colors relative z-10">
                        @if($step4)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($step3)
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        @else
                            <span class="text-sm font-semibold">3</span>
                        @endif
                    </div>
                    <div class="md:text-center mt-1 md:mt-0">
                        <p class="text-sm font-bold {{ $step3 ? 'text-gray-900' : 'text-gray-500' }}">Under Review</p>
                        <p class="text-xs text-gray-500 mt-0.5">Admin verifies your license.</p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex md:flex-col items-start md:items-center gap-4 md:gap-2 relative w-full md:w-1/4">
                    <div class="hidden md:block absolute top-4 left-0 w-1/2 h-0.5 {{ $step3 ? 'bg-green-500' : 'bg-gray-200' }} -z-10"></div>
                    
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 border-2 {{ $step4 ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 text-gray-400' }} transition-colors relative z-10">
                        @if($step4)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="text-sm font-semibold">4</span>
                        @endif
                    </div>
                    <div class="md:text-center mt-1 md:mt-0">
                        <p class="text-sm font-bold {{ $step4 ? 'text-gray-900' : 'text-gray-500' }}">Verified Doctor</p>
                        <p class="text-xs text-gray-500 mt-0.5">Account fully activated.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

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
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wider mb-1">Next Upcoming Appointment</p>
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $totalPatients }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Total Patients</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $totalCount }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Total Appointments</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <p class="text-3xl font-bold text-gray-900">{{ $weekAppointments }}</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">This Week's Appointments</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col justify-center items-start">
            <span class="bg-green-100 text-green-700 text-2xl font-bold px-3 py-1 rounded-full mb-1">{{ $completionRate }}%</span>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Completion Rate</p>
        </div>
    </div>

    {{-- Today's Appointments --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
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
            <p class="text-gray-500 dark:text-gray-400 text-sm">No appointments scheduled for today.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
