@extends($layout)
@section('title', 'Help Requests')
@section('page-title', 'Help Requests')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Help Requests</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if(auth()->user()->role === 'doctor')
                    Direct assistance requests from patients
                @else
                    Your direct assistance requests to doctors
                @endif
            </p>
        </div>
        @if(auth()->user()->role === 'patient')
        <div>
            <a href="{{ route('help-requests.create') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Request
            </a>
        </div>
        @endif
    </div>

    @if($requests->count() > 0)
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden text-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        {{ auth()->user()->role === 'doctor' ? 'Patient' : 'Doctor' }}
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($requests as $req)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900">{{ $req->suggested_title }}</span>
                            <span class="text-xs text-gray-500 mt-0.5">{{ $req->messages->count() }} messages</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php $person = auth()->user()->role === 'doctor' ? $req->user : $req->doctor; @endphp
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center text-xs font-bold {{ $person->role === 'doctor' ? 'text-blue-700' : 'text-green-700' }} overflow-hidden">
                                @if($person->profile_photo)
                                    <img src="{{ Storage::url($person->profile_photo) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($person->fname, 0, 1)) }}
                                @endif
                            </div>
                            <span class="font-medium text-gray-900">{{ $person->display_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize
                            @if($req->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($req->status === 'accepted') bg-green-100 text-green-800
                            @elseif($req->status === 'declined') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif
                        ">
                            {{ $req->status }}
                            @if($req->status === 'resolved')
                                <svg class="ml-1 -mr-0.5 w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('help-requests.show', $req->id) }}" class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-800 hover:bg-green-50 px-3 py-1.5 rounded-lg transition-colors">
                            View Thread
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $requests->links() }}
    </div>
    @else
    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No help requests</h3>
        <p class="text-gray-500 text-sm mb-6">
            @if(auth()->user()->role === 'doctor')
                You don't have any incoming requests right now.
            @else
                You haven't sent any direct help requests to a doctor yet.
            @endif
        </p>
        @if(auth()->user()->role === 'patient')
        <a href="{{ route('help-requests.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors">
            Send a Request
        </a>
        @endif
    </div>
    @endif

</div>
@endsection
