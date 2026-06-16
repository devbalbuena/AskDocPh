@extends('layouts.admin')
@section('title', 'ID Verifications')
@section('page-title', 'Patient Verification')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">ID Verification Requests</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.id-verification.index', ['status' => 'pending']) }}" class="text-sm px-4 py-2 rounded-xl font-medium transition-colors border {{ $status === 'pending' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">Pending</a>
            <a href="{{ route('admin.id-verification.index', ['status' => 'approved']) }}" class="text-sm px-4 py-2 rounded-xl font-medium transition-colors border {{ $status === 'approved' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">Approved</a>
            <a href="{{ route('admin.id-verification.index', ['status' => 'rejected']) }}" class="text-sm px-4 py-2 rounded-xl font-medium transition-colors border {{ $status === 'rejected' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">Rejected</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="h-48 bg-gray-100 relative group cursor-pointer" onclick="openImageModal('{{ Storage::url($user->id_document_path) }}')">
                @if(strtolower(pathinfo($user->id_document_path, PATHINFO_EXTENSION)) === 'pdf')
                    <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="text-sm font-medium">View PDF Document</span>
                    </div>
                @else
                    <img src="{{ Storage::url($user->id_document_path) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center transition-all">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </div>
                @endif
            </div>
            
            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-900 truncate pr-2">{{ $user->display_name }}</h3>
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                <div class="text-sm text-gray-600 mb-4 flex-1 space-y-1">
                    <p><span class="font-semibold text-gray-500">Email:</span> {{ $user->email }}</p>
                    <p><span class="font-semibold text-gray-500">Username:</span> {{ $user->username }}</p>
                </div>
                
                @if($status === 'pending')
                <div class="flex gap-2 mt-auto">
                    <form action="{{ route('admin.id-verification.update', $user->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Approve</button>
                    </form>
                    <form action="{{ route('admin.id-verification.update', $user->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Reject</button>
                    </form>
                </div>
                @elseif($status === 'rejected')
                <div class="mt-auto">
                     <form action="{{ route('admin.id-verification.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="w-full bg-gray-100 border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200 text-gray-700 text-sm font-medium py-2 rounded-lg transition-colors">Re-evaluate & Approve</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-500">
            No {{ $status }} verification requests found.
        </div>
        @endforelse
    </div>

    {{ $users->links() }}
</div>

{{-- Image Modal --}}
<div id="image-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-10" onclick="closeImageModal()">
    <div class="relative max-w-5xl w-full h-full flex flex-col items-center justify-center" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute top-0 right-0 p-2 text-white hover:text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div id="modal-content" class="w-full h-full max-h-[85vh] flex items-center justify-center">
            {{-- Content injected via JS --}}
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImageModal(url) {
    const isPdf = url.toLowerCase().endsWith('.pdf');
    const modalContent = document.getElementById('modal-content');
    
    if (isPdf) {
        modalContent.innerHTML = `<iframe src="${url}" class="w-full h-full bg-white rounded-xl"></iframe>`;
    } else {
        modalContent.innerHTML = `<img src="${url}" class="max-w-full max-h-full object-contain rounded-lg">`;
    }
    
    document.getElementById('image-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
    document.getElementById('modal-content').innerHTML = '';
    document.body.style.overflow = 'auto';
}
</script>
@endpush
@endsection
