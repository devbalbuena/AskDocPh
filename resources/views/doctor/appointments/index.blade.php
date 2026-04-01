@extends('layouts.doctor')
@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="space-y-5">
    {{-- Status Filter Tabs --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1 w-fit flex-wrap">
        @foreach(['all'=>'All','pending'=>'Pending','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled'] as $val => $label)
        <a href="{{ route('doctor.appointments.index', ['status' => $val]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $status === $val ? 'bg-green-600 text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Appointments --}}
    <div class="space-y-3">
        @forelse($appointments as $appt)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-300 flex-shrink-0">
                    {{ strtoupper(substr($appt->patient->fname ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-gray-900 font-semibold">{{ $appt->patient->display_name ?? 'Unknown' }}</p>
                    <p class="text-gray-500 text-sm mt-0.5">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, M d Y') }} • {{ substr($appt->start_time, 0, 5) }}</p>
                    <p class="text-gray-500 text-xs mt-0.5 capitalize">{{ $appt->type }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="
                    @if($appt->status==='pending')   bg-yellow-100 text-yellow-700
                    @elseif($appt->status==='confirmed') bg-green-100 text-green-700
                    @elseif($appt->status==='completed') bg-blue-100 text-blue-700
                    @else bg-red-100 text-red-700 @endif
                    text-xs px-3 py-1 rounded-full capitalize font-medium">
                    {{ $appt->status }}
                </span>
                <a href="{{ route('doctor.appointments.show', $appt) }}" class="bg-green-600 hover:bg-green-700 text-gray-900 text-sm px-4 py-2 rounded-lg transition-colors">View</a>
            </div>
        </div>
        @empty
        <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
            <p class="text-gray-500 text-sm">No appointments found.</p>
        </div>
        @endforelse
    </div>
    @if($appointments->hasPages())
    <div>{{ $appointments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
