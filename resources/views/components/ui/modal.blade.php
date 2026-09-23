{{-- Modal Component --}}
{{-- Props: $title, $size = 'md', $open = false, $closeOnOverlay = true, $id = null --}}
{{-- Slots: header, body, footer --}}
@php
    $sizes = ['sm' => 'max-w-md', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];
    $modalId = $id ?? 'modal-' . uniqid();
@endphp
<div 
    id="{{ $modalId }}"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm {{ $open ? '' : 'hidden' }}" 
    @if($closeOnOverlay) onclick="this.classList.add('hidden')" @endif
>
    <div class="w-full {{ $sizes[$size] }} bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 transform transition-all">
        @if($header)
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">{{ $header }}</h3>
                <button type="button" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
        <div class="p-5 max-h-[70vh] overflow-y-auto">{{ $body }}</div>
        @if($footer)
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2">{{ $footer }}</div>
        @endif
    </div>
</div>