@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label' => 'Total Patients',         'value' => $totalPatients,        'color' => 'blue',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Approved Doctors',       'value' => $totalDoctors,          'color' => 'green',  'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['label' => 'Pending Applications',   'value' => $pendingApplications,   'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Open Crisis Reports',    'value' => $crisisReports,         'color' => 'red',    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ];
        $colorMap = ['blue' => 'bg-blue-100 text-blue-700', 'green' => 'bg-green-100 text-green-700', 'yellow' => 'bg-yellow-100 text-yellow-700', 'red' => 'bg-red-100 text-red-700'];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $colorMap[$card['color']] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($card['value']) }}</p>
                <p class="text-gray-500 text-sm">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Chart + Tables row --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Registrations Chart --}}
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">New Registrations — Last 7 Days</h3>
            <canvas id="registrationsChart" height="120"></canvas>
        </div>

        {{-- Recent Crisis Reports --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Recent Crisis Reports</h3>
                <a href="{{ route('admin.crisis.index') }}" class="text-green-600 text-xs hover:text-green-700">View all →</a>
            </div>
            @forelse($recentCrisisReports as $report)
            <div class="py-3 border-b border-gray-200 last:border-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $report->user->display_name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $report->description }}</p>
                    </div>
                    <span class="{{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($report->status === 'responding' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }} text-xs px-2 py-0.5 rounded-full capitalize ml-2 flex-shrink-0">
                        {{ $report->status }}
                    </span>
                </div>
                <p class="text-xs text-gray-600 mt-1">{{ $report->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-4">No crisis reports.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Doctor Applications --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Recent Doctor Applications</h3>
            <a href="{{ route('admin.doctor-applications.index') }}" class="text-green-600 text-xs hover:text-green-700">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="text-left px-5 py-3">Applicant</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Submitted</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-right px-5 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApplications as $app)
                    <tr class="border-b border-gray-200/50 hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-900 font-medium">{{ $app->user->display_name ?? 'Unknown' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $app->user->email ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <span class="{{ $app->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($app->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }} text-xs px-2.5 py-1 rounded-full capitalize">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.doctor-applications.show', $app) }}" class="text-green-600 hover:text-green-700 text-xs transition-colors">View →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-6 text-center text-gray-500">No applications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartData = @json($registrationsLastWeek);
const ctx = document.getElementById('registrationsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [{
            label: 'New Registrations',
            data: chartData.map(d => d.count),
            backgroundColor: '#16a34a',
            borderColor: '#16a34a',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af', stepSize: 1 }, beginAtZero: true }
        }
    }
});
</script>
@endpush
