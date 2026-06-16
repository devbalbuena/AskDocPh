@extends($layout)
@section('title', 'Post Analytics')
@section('page-title', 'My Post Analytics')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Post Engagement Analytics</h2>
            <p class="text-sm text-gray-500">Track how your audience is reacting to your content.</p>
        </div>
    </div>

    {{-- Top Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <div class="flex items-center gap-3 text-red-600 mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                <h3 class="text-sm font-semibold uppercase tracking-wider">Total Likes</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalLikes) }}</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <div class="flex items-center gap-3 text-blue-600 mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>
                <h3 class="text-sm font-semibold uppercase tracking-wider">Total Comments</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalComments) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <div class="flex items-center gap-3 text-green-600 mb-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/></svg>
                <h3 class="text-sm font-semibold uppercase tracking-wider">Total Shares</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalShares) }}</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Trend Chart (Line Chart over 7 days) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-6">Engagement Overview (Last 7 Days)</h3>
            <div class="relative h-72 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Reaction Breakdown (Doughnut Chart) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-6">Reaction Types Breakdown</h3>
            <div class="relative h-64 w-full flex justify-center">
                @if(empty($reactionBreakdown))
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">No reaction data available yet.</p>
                    </div>
                @else
                    <canvas id="reactionChart"></canvas>
                @endif
            </div>
        </div>

    </div>

    {{-- Top Performing Posts --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Top Performing Posts</h3>
        </div>
        
        @if($topPosts->count())
        <div class="divide-y divide-gray-100">
            @foreach($topPosts as $post)
            <div class="p-6 flex flex-col md:flex-row gap-5 hover:bg-gray-50 transition-colors">
                
                {{-- Media Thumbnail (if exists) --}}
                <div class="w-full md:w-32 h-24 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0 border border-gray-200">
                    @if($post->media->count() > 0)
                        <img src="{{ Storage::url($post->media[0]->file_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 p-3 text-center">
                            <span class="text-xs truncate font-medium">{{ mb_substr($post->text_content, 0, 15) }}...</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0 flex flex-col">
                    <p class="text-sm text-gray-800 line-clamp-2 md:line-clamp-3 leading-relaxed mb-3">{{ $post->text_content }}</p>
                    
                    <div class="mt-auto flex items-center gap-6 text-sm">
                        <span class="flex items-center gap-1.5 text-gray-600 font-medium bg-red-50 text-red-700 px-3 py-1 rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                            {{ $post->likes_count }}
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-600 font-medium bg-blue-50 text-blue-700 px-3 py-1 rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" /></svg>
                            {{ $post->comments_count }}
                        </span>
                        <span class="flex items-center gap-1.5 text-gray-600 font-medium bg-green-50 text-green-700 px-3 py-1 rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/></svg>
                            {{ $post->shares_count }}
                        </span>

                        <span class="text-xs text-gray-400 ml-auto flex items-center gap-1 bg-white border border-gray-200 px-2 py-1 rounded-lg shadow-sm">
                            Score: <strong class="text-green-600 ml-0.5">{{ $post->engagement }}</strong>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
            <div class="p-12 text-center text-gray-500">
                You haven't posted anything yet, or no posts have received engagement.
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ─── Trend Chart (Line) ────────────────────────────────────────────────
        const trendCtx = document.getElementById('trendChart')?.getContext('2d');
        if(trendCtx) {
            const rawTrendData = @json($trend);
            
            const labels = rawTrendData.map(d => d.date);
            const likes = rawTrendData.map(d => d.likes);
            const comments = rawTrendData.map(d => d.comments);
            const shares = rawTrendData.map(d => d.shares);

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Likes', data: likes, borderColor: '#EF4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', borderWidth: 3, fill: true, tension: 0.4 },
                        { label: 'Comments', data: comments, borderColor: '#3B82F6', backgroundColor: 'transparent', borderWidth: 2, tension: 0.4 },
                        { label: 'Shares', data: shares, borderColor: '#10B981', backgroundColor: 'transparent', borderWidth: 2, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: {family: 'Inter'} } },
                        tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(17, 24, 39, 0.9)', titleFont: {family: 'Inter'}, bodyFont: {family: 'Inter'} }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    },
                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                }
            });
        }

        // ─── Reactions Chart (Doughnut) ──────────────────────────────────────────
        const reactionCtx = document.getElementById('reactionChart')?.getContext('2d');
        if(reactionCtx) {
            const rawReactions = @json($reactionBreakdown);
            
            // Standardizing colors for reactions
            const colorMap = {
                'like': '#EF4444',
                'love': '#F43F5E',
                'support': '#8B5CF6',
                'insightful': '#F59E0B'
            };

            const labels = Object.keys(rawReactions).map(r => r.charAt(0).toUpperCase() + r.slice(1));
            const data = Object.values(rawReactions);
            const bgColors = Object.keys(rawReactions).map(r => colorMap[r] || '#9CA3AF');

            if(data.length > 0) {
                new Chart(reactionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: bgColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: {family: 'Inter'} } },
                            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', bodyFont: {family: 'Inter'} }
                        }
                    }
                });
            }
        }
    });
</script>
@endpush
@endsection
