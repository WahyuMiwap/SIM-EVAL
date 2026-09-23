{{-- Button Component --}}
{{-- Props: $variant = 'primary', $size = 'md', $icon = null, $href = null, $class = '', $disabled = false, $type = 'button' --}}
@php
    $variants = [
        'primary' => 'bg-primary text-white border-primary hover:bg-primary-dark',
        'secondary' => 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-600 dark:hover:bg-slate-700',
        'danger' => 'bg-danger text-white border-danger hover:bg-red-600',
        'ghost' => 'bg-transparent text-primary hover:bg-primary/10 border-transparent',
        'success' => 'bg-success text-white border-success hover:bg-green-600',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'icon' => 'p-2 w-10 h-10',
    ];
@endphp
@if($href)
    <a 
        href="{{ $href }}" 
        class="inline-flex items-center justify-center gap-1.5 font-semibold rounded-lg border-1.5 transition-all {{ $variants[$variant] }} {{ $sizes[$size] }} {{ $class }}" 
        {{ $disabled ? 'aria-disabled="true" tabindex="-1" pointer-events-none opacity-50' : '' }}
    >
        @if($icon) <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{{ $icon }}</svg> @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        class="inline-flex items-center justify-center gap-1.5 font-semibold rounded-lg border-1.5 transition-all {{ $variants[$variant] }} {{ $sizes[$size] }} {{ $class }}" 
        {{ $disabled ? 'disabled' : '' }}
    >
        @if($icon) <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{{ $icon }}</svg> @endif
        {{ $slot }}
    </button>
@endif