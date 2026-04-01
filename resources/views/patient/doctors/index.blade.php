@extends('layouts.patient')
@section('title', 'Find Doctors')
@section('page-title', 'Find Doctors')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($doctors as $doctor)
        @php
            $titles = $doctor->doctorApplications->flatMap(fn($a) => $a->professionalTitles)->pluck('professionalTitle.name')->filter()->implode(', ');
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col items-center text-center hover:border-green-300 transition-all group">
            <div class="w-20 h-20 rounded-2xl bg-green-100 flex items-center justify-center text-2xl font-bold text-green-700 mb-4 group-hover:bg-green-600/50 transition-colors overflow-hidden">
                @if($doctor->profile_photo)
                <img src="{{ asset('storage/'.$doctor->profile_photo) }}" alt="{{ $doctor->display_name }}" class="w-full h-full object-cover">
                @else
                {{ strtoupper(substr($doctor->fname, 0, 1)) }}{{ strtoupper(substr($doctor->lname, 0, 1)) }}
                @endif
            </div>
            <p class="text-gray-900 font-semibold">Dr. {{ $doctor->display_name }}</p>
            @if($titles)
            <p class="text-green-600 text-xs mt-1">{{ $titles }}</p>
            @endif
            <p class="text-gray-500 text-xs mt-1">{{ $doctor->bio ? Str::limit($doctor->bio, 60) : 'Mental Health Professional' }}</p>
            <a href="{{ route('patient.doctors.schedule', $doctor) }}" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-gray-900 text-sm font-medium py-2.5 rounded-lg transition-colors">
                Book Appointment
            </a>
        </div>
        @empty
        <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center">
            <p class="text-gray-500">No approved doctors available yet.</p>
        </div>
        @endforelse
    </div>
    @if($doctors->hasPages())
    <div>{{ $doctors->links() }}</div>
    @endif
</div>
@endsection
