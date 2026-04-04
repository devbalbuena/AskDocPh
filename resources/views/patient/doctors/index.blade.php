@extends('layouts.patient')
@section('title', 'Find Doctors')
@section('page-title', 'Find Doctors')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex flex-wrap items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <select name="specialization" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 min-w-[200px]">
                <option value="">All Specializations</option>
                @foreach($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer bg-gray-50 border border-gray-300 px-4 py-2.5 rounded-xl hover:bg-gray-100 transition-colors">
            <input type="checkbox" name="available_week" value="1" {{ request('available_week') ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500" onchange="this.form.submit()">
            Available This Week
        </label>
        <div class="ml-auto">
            @if(request('specialization') || request('available_week'))
            <a href="{{ route('patient.doctors.index') }}" class="text-sm text-gray-500 hover:text-red-500">Clear Filters</a>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($doctors as $doctor)
        @php
            $titles = $doctor->doctorApplications->flatMap(fn($a) => $a->professionalTitles)->pluck('professionalTitle.name')->filter()->implode(', ');
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center hover:border-green-300 transition-all group">
            <div class="w-20 h-20 rounded-2xl bg-green-100 flex items-center justify-center text-2xl font-bold text-green-700 mb-4 group-hover:bg-green-600/50 transition-colors overflow-hidden">
                @if($doctor->profile_photo)
                <img src="{{ asset('storage/'.$doctor->profile_photo) }}" alt="{{ $doctor->display_name }}" class="w-full h-full object-cover">
                @else
                {{ strtoupper(substr($doctor->fname, 0, 1)) }}{{ strtoupper(substr($doctor->lname, 0, 1)) }}
                @endif
            </div>
            <p class="text-gray-900 font-semibold flex items-center justify-center gap-1">
                Dr. {{ $doctor->display_name }}
                @if($doctor->isVerifiedDoctor())
                <span class="text-blue-500" title="Verified Doctor">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </span>
                @endif
            </p>
            @php $avgRating = $doctor->doctorReviews->avg('rating'); @endphp
            @if($avgRating > 0)
            <div class="flex items-center gap-1 mt-1 text-xs">
                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="font-bold text-gray-700">{{ number_format($avgRating, 1) }}</span>
                <span class="text-gray-400">({{ $doctor->doctorReviews->count() }})</span>
            </div>
            @else
            <div class="text-xs text-gray-400 mt-1">No reviews yet</div>
            @endif
            @if($doctor->next_available)
                <p class="text-green-600 text-sm mt-1.5">Next available: {{ $doctor->next_available }}</p>
            @else
                <p class="text-gray-400 text-sm mt-1.5">No availability set yet</p>
            @endif
            @if($titles)
            <p class="text-green-600 text-xs mt-1">{{ $titles }}</p>
            @endif
            @php
            $professional = json_decode($doctor->bio ?? '{}', true) ?? [];
            @endphp
            @if(!empty($professional['specialization']))
            <p class="text-gray-500 text-sm mt-1">
                {{ $professional['specialization'] }}
            </p>
            @endif
            <a href="{{ route('patient.doctors.schedule', $doctor) }}" class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-3 rounded-xl transition-colors shadow-sm block text-center">
                Book Appointment
            </a>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <p class="text-gray-500">No approved doctors available yet.</p>
        </div>
        @endforelse
    </div>
    @if($doctors->hasPages())
    <div>{{ $doctors->links() }}</div>
    @endif
</div>
@endsection
