{{-- Toast Component --}}
{{-- Props: $message, $type = 'success', $duration = 3000 --}}
@php
    $types = [
        'success' => 'bg-success text-white',
        'error' => 'bg-danger text-white',
        'info' => 'bg-primary text-white',
    ];
    $icons = [
        'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>',
        'error' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
        'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];
@endphp
<div class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg {{ $types[$type] }} animate-slide-in-right" role="alert">
    {{ $icons[$type] }}
    <span class="text-sm font-medium">{{ $message }}</span>
</div>
<script>
    setTimeout(() => {
        const el = document.currentScript.parentElement;
        el.classList.add('opacity-0', 'translate-x-8', 'transition-all', 'duration-300');
        setTimeout(() => el.remove(), 300);
    }, {{ $duration }});
</script>