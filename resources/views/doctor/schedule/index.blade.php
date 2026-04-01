@extends('layouts.doctor')
@section('title', 'My Schedule')
@section('page-title', 'My Schedule')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Current Slots --}}
    <div class="xl:col-span-2 space-y-3">
        <h3 class="text-base font-semibold text-white">Current Availability Slots</h3>
        @php $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']; @endphp
        @foreach($days as $day)
        @php $daySlots = $schedules->where('day_of_week', $day); @endphp
        @if($daySlots->isNotEmpty())
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-700 bg-gray-900/40">
                <p class="text-sm font-medium text-white capitalize">{{ $day }}</p>
            </div>
            @foreach($daySlots as $slot)
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700/50 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="{{ $slot->is_available ? 'bg-green-500/20 text-green-400' : 'bg-gray-600/40 text-gray-400' }} text-xs px-2 py-0.5 rounded-full">
                        {{ $slot->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                    <span class="text-white text-sm">{{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}</span>
                    <span class="text-gray-500 text-xs">{{ $slot->slot_duration_minutes }}min slots</span>
                </div>
                <form method="POST" action="{{ route('doctor.schedule.destroy', $slot) }}" onsubmit="return confirm('Remove this slot?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">Remove</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
        @endforeach
        @if($schedules->isEmpty())
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-10 text-center">
            <p class="text-gray-400 text-sm">No schedule slots yet. Add your availability.</p>
        </div>
        @endif
    </div>

    {{-- Add Slot Form --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 h-fit">
        <h3 class="text-base font-semibold text-white mb-4">Add Availability Slot</h3>
        <form method="POST" action="{{ route('doctor.schedule.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Day</label>
                <select name="day_of_week" class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
                    @foreach($days as $day)
                    <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                           class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                           class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="block text-xs text-gray-400 uppercase tracking-wider mb-1.5">Slot Duration</label>
                <select name="slot_duration_minutes" class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500">
                    <option value="15">15 minutes</option>
                    <option value="30" selected>30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">60 minutes</option>
                </select>
            </div>
            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-2 text-red-400 text-xs">
                @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
            </div>
            @endif
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 rounded-xl text-sm transition-colors">
                Add Slot
            </button>
        </form>
    </div>
</div>
@endsection
