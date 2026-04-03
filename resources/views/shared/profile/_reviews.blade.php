<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mt-6">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Patient Reviews
        </h2>
        <div class="flex items-center gap-1.5">
            <span class="text-xl font-bold text-gray-900">{{ number_format($doctorAverageRating, 1) }}</span>
            <span class="text-sm text-gray-500">out of 5</span>
        </div>
    </div>
    
    <div class="divide-y divide-gray-100">
        @forelse($doctorReviews as $review)
        <div class="p-6">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="flex items-center gap-3 w-full">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700 flex-shrink-0">
                        {{ strtoupper(substr($review->patient->fname, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $review->is_anonymous ? 'Anonymous Patient' : $review->patient->display_name }}</h4>
                            <span class="text-[10px] text-gray-400">&bull; {{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center text-yellow-400 mt-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            @if($review->review_content)
            <p class="text-sm text-gray-700 mt-3 leading-relaxed bg-gray-50/50 p-4 rounded-xl border border-gray-100">{{ $review->review_content }}</p>
            @endif
        </div>
        @empty
        <div class="p-10 text-center text-gray-500 text-sm">
            This doctor hasn't received any reviews yet.
        </div>
        @endforelse
    </div>
</div>
