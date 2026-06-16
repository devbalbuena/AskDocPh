@extends('layouts.patient')
@section('title', 'Mood Tracker')
@section('page-title', 'Mood Tracker')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Mood Tracker</h2>
                <p class="text-sm text-gray-500 mt-0.5">Track how you feel each day to spot patterns over time.</p>
            </div>
        </div>
        <button onclick="document.getElementById('log-mood-modal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Log Today's Mood
        </button>
    </div>

    {{-- Chart & Entry Split --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left: Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <h3 class="text-base font-bold text-gray-900 mb-6">Mood History (Last 14 Days)</h3>
            <div class="relative flex-1 w-full" style="min-height: 300px;">
                @if(empty($chartData))
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm font-medium text-gray-500">Not enough data to display chart.</p>
                        <p class="text-xs mt-1">Start logging your mood to see trends.</p>
                    </div>
                @else
                    <canvas id="moodChart"></canvas>
                @endif
            </div>
        </div>

        {{-- Right: Recent Entries Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col overflow-hidden max-h-[400px]">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Recent Logs</h3>
                <a href="{{ route('patient.mood.history') }}" class="text-xs font-semibold text-green-600 hover:text-green-700">View All</a>
            </div>
            
            <div class="flex-1 overflow-y-auto p-2">
                @if($entries->count())
                    <div class="space-y-1">
                        @foreach($entries as $entry)
                        <div class="p-3 hover:bg-gray-50 rounded-xl transition-colors">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-gray-500">{{ $entry->entry_date->format('l, M j') }}</span>
                                @php
                                    $emoji = match($entry->mood_score) {
                                        1 => '😢', 2 => '🙁', 3 => '😐', 4 => '🙂', 5 => '😄', default => '😐'
                                    };
                                    $color = match($entry->mood_score) {
                                        1 => 'text-red-500 border-red-200 bg-red-50',
                                        2 => 'text-orange-500 border-orange-200 bg-orange-50',
                                        3 => 'text-yellow-500 border-yellow-200 bg-yellow-50',
                                        4 => 'text-green-500 border-green-200 bg-green-50',
                                        5 => 'text-blue-500 border-blue-200 bg-blue-50',
                                        default => 'text-gray-500 border-gray-200 bg-gray-50'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $color }} inline-flex items-center gap-1.5">
                                    <span class="text-sm">{{ $emoji }}</span>
                                    Score: {{ $entry->mood_score }}
                                </span>
                            </div>
                            @if($entry->notes)
                            <p class="text-[13px] text-gray-700 bg-white border border-gray-100 rounded-lg p-2.5 mt-2 line-clamp-3 leading-relaxed shadow-sm">{{ $entry->notes }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500 flex flex-col items-center">
                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-sm font-medium">No logs recorded</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal for Logging Mood --}}
<div id="log-mood-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4 transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">How are you feeling?</h3>
            <button onclick="document.getElementById('log-mood-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 hover:bg-gray-200 p-1.5 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('patient.mood.store') }}" class="p-6">
            @csrf
            
            <input type="hidden" id="selected_date" name="entry_date" value="{{ now()->toDateString() }}">
            <input type="hidden" id="mood_score" name="mood_score" value="3" required>

            <div class="flex items-center justify-between mb-8 gap-2">
                <button type="button" onclick="selectScore(1)" id="btn-1" class="mood-btn flex-1 flex flex-col items-center p-3 rounded-2xl hover:bg-red-50 border-2 border-transparent transition-all grayscale opacity-60 hover:grayscale-0 hover:opacity-100">
                    <span class="text-4xl mb-1 transform hover:scale-110 transition-transform">😢</span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Awful</span>
                </button>
                <button type="button" onclick="selectScore(2)" id="btn-2" class="mood-btn flex-1 flex flex-col items-center p-3 rounded-2xl hover:bg-orange-50 border-2 border-transparent transition-all grayscale opacity-60 hover:grayscale-0 hover:opacity-100">
                    <span class="text-4xl mb-1 transform hover:scale-110 transition-transform">🙁</span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Bad</span>
                </button>
                <button type="button" onclick="selectScore(3)" id="btn-3" class="mood-btn flex-1 flex flex-col items-center p-3 rounded-2xl bg-yellow-50 border-2 border-yellow-400 transition-all shadow-sm">
                    <span class="text-4xl mb-1 transform scale-110 transition-transform">😐</span>
                    <span class="text-[10px] font-bold text-yellow-700 uppercase tracking-wide">Okay</span>
                </button>
                <button type="button" onclick="selectScore(4)" id="btn-4" class="mood-btn flex-1 flex flex-col items-center p-3 rounded-2xl hover:bg-green-50 border-2 border-transparent transition-all grayscale opacity-60 hover:grayscale-0 hover:opacity-100">
                    <span class="text-4xl mb-1 transform hover:scale-110 transition-transform">🙂</span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Good</span>
                </button>
                <button type="button" onclick="selectScore(5)" id="btn-5" class="mood-btn flex-1 flex flex-col items-center p-3 rounded-2xl hover:bg-blue-50 border-2 border-transparent transition-all grayscale opacity-60 hover:grayscale-0 hover:opacity-100">
                    <span class="text-4xl mb-1 transform hover:scale-110 transition-transform">😄</span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Great</span>
                </button>
            </div>

            <div class="mb-5">
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3" maxlength="1000"
                          class="w-full border border-gray-200 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-xl px-4 py-3 outline-none transition resize-none text-sm"
                          placeholder="What's making you feel this way? Add any details you want to remember."></textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="date" id="date_picker" class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 text-gray-600"
                       value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" onchange="document.getElementById('selected_date').value = this.value">
                <button type="submit" class="flex-[2] bg-gray-900 hover:bg-black text-white text-sm font-semibold py-2.5 rounded-xl transition-colors shadow-sm">
                    Save Entry
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function selectScore(score) {
        document.getElementById('mood_score').value = score;
        
        // Reset all
        document.querySelectorAll('.mood-btn').forEach(btn => {
            btn.classList.add('grayscale', 'opacity-60', 'border-transparent');
            btn.classList.remove('border-red-400', 'bg-red-50', 'border-orange-400', 'bg-orange-50', 'border-yellow-400', 'bg-yellow-50', 'border-green-400', 'bg-green-50', 'border-blue-400', 'bg-blue-50', 'shadow-sm');
            btn.querySelector('span:first-child').classList.remove('scale-110');
            btn.querySelector('span:last-child').classList.replace('text-red-700', 'text-gray-500');
            btn.querySelector('span:last-child').classList.replace('text-orange-700', 'text-gray-500');
            btn.querySelector('span:last-child').classList.replace('text-yellow-700', 'text-gray-500');
            btn.querySelector('span:last-child').classList.replace('text-green-700', 'text-gray-500');
            btn.querySelector('span:last-child').classList.replace('text-blue-700', 'text-gray-500');
        });

        const activeBtn = document.getElementById('btn-' + score);
        activeBtn.classList.remove('grayscale', 'opacity-60', 'border-transparent');
        activeBtn.classList.add('shadow-sm');
        activeBtn.querySelector('span:first-child').classList.add('scale-110');
        
        const textSpan = activeBtn.querySelector('span:last-child');
        textSpan.classList.remove('text-gray-500');
        
        if (score === 1) { activeBtn.classList.add('border-red-400', 'bg-red-50'); textSpan.classList.add('text-red-700'); }
        if (score === 2) { activeBtn.classList.add('border-orange-400', 'bg-orange-50'); textSpan.classList.add('text-orange-700'); }
        if (score === 3) { activeBtn.classList.add('border-yellow-400', 'bg-yellow-50'); textSpan.classList.add('text-yellow-700'); }
        if (score === 4) { activeBtn.classList.add('border-green-400', 'bg-green-50'); textSpan.classList.add('text-green-700'); }
        if (score === 5) { activeBtn.classList.add('border-blue-400', 'bg-blue-50'); textSpan.classList.add('text-blue-700'); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(!empty($chartData))
        const ctx = document.getElementById('moodChart').getContext('2d');
        
        // Define emojis matching y-axis values 1-5
        const emojis = { 1:'😢', 2:'🙁', 3:'😐', 4:'🙂', 5:'😄' };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Mood Score',
                    data: @json($chartData),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleFont: {family: 'Inter'},
                        bodyFont: {family: 'Inter', size: 14},
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                return ' Score: ' + val + ' ' + emojis[val];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 1, max: 5,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            stepSize: 1,
                            font: { size: 16 },
                            callback: function(value) { return emojis[value] || ''; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#6B7280' }
                    }
                },
                layout: { padding: { left: -5, bottom: 0 } }
            }
        });
        @endif
    });
</script>
@endpush
@endsection
