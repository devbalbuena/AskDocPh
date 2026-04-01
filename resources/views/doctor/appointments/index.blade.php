@extends('layouts.doctor')
@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="space-y-5">
    {{-- Status Filter Tabs --}}
    <div class="flex gap-1 bg-gray-800 border border-gray-700 rounded-xl p-1 w-fit flex-wrap">
        @foreach(['all'=>'All','pending'=>'Pending','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
        <a href="{{ route('doctor.appointments.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-purple-600 text-white' : 'text-gray-400 hover:text-white' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Appointments --}}
    <div class="space-y-3">
        @forelse($appointments as $appt)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-blue-600/30 flex items-center justify-center text-sm font-bold text-blue-300 flex-shrink-0">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-400 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} • {{ substr($appt->start_time, 0, 5) }}</p>
                    <p class="text-gray-500 text-xs mt-0.5 capitalize">{{ $appt->type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="
                    @if($appt->status==='pending')   bg-yellow-500/20 text-yellow-400
                    @elseif($appt->status==='confirmed') bg-green-500/20 text-green-400
                    @elseif($appt->status==='completed') bg-blue-500/20 text-blue-400
                    @else bg-red-500/20 text-red-400 @endif
                    text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
                <a href="{{ route('doctor.appointments.show', $appt) }}" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-4 py-2 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No appointments found.</p>
        </div>
        @endforelse
    </div>
    @if($appointments->hasPages())
    <div>{{ $appointments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
