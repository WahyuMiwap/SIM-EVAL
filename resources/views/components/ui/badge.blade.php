{{-- Badge Component --}}
{{-- Props: $variant = 'gray', $dot = false, $class = '' --}}
@php
    $variants = [
        'success' => 'bg-success-light text-success-dark',
        'warning' => 'bg-warning-light text-warning-dark',
        'danger' => 'bg-danger-light text-danger-dark',
        'info' => 'bg-info-light text-info-dark',
        'primary' => 'bg-primary-light text-primary',
        'gray' => 'bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
    ];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $variants[$variant] }} {{ $class }}">
    @if($dot) <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span> @endif
    {{ $slot }}
</span>