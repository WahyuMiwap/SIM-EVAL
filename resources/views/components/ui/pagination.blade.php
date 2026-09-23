{{-- Pagination Component --}}
{{-- Props: $paginator (LengthAwarePaginator), $class = '' --}}
@if($paginator->hasPages())
<div class="flex items-center justify-center gap-1 {{ $class }}">
    @if($paginator->onFirstPage())
        <span class="page-btn opacity-30 cursor-not-allowed"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
    @endif
    @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="page-btn {{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
    @endforeach
    @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
    @else
        <span class="page-btn opacity-30 cursor-not-allowed"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
    @endif
</div>
@endif