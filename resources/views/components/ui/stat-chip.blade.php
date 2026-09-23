{{-- Stat Chip Component --}}
{{-- Props: $label, $value, $suffix = '', $trend = null, $trendValue = '', $icon = null, $color = 'primary', $badge = null, $class = '' --}}
@php
    $colors = [
        'primary' => 'bg-primary-light text-primary border-primary/20',
        'cyan' => 'bg-info-light text-info-dark border-info/20',
        'emerald' => 'bg-success-light text-success-dark border-success/20',
        'purple' => 'bg-purple-light text-purple-dark border-purple/20',
    ];
    $icons = [
        'users' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
        'target' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'check-circle' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'trending-up' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
    ];
@endphp
<div class="p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl {{ $class }}">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-xl font-bold text-slate-900 dark:text-white tabular-nums">{{ $value }}</span>
                @if($suffix) <span class="text-xs text-slate-500">{{ $suffix }}</span> @endif
            </div>
            @if($trend)
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="badge badge-{{ $trend === 'up' ? 'success' : ($trend === 'down' ? 'danger' : 'gray') }} text-[10px]">
                        @if($trend === 'up') <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        @elseif($trend === 'down') <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V4"/></svg>
                        @endif
                        {{ $trendValue }}
                    </span>
                </div>
            @endif
        </div>
        @if($icon && isset($icons[$icon]))
            <div class="w-10 h-10 rounded-lg {{ $colors[$color] }} flex items-center justify-center flex-shrink-0">
                {{ $icons[$icon] }}
            </div>
        @endif
    </div>
    @if($badge)
        <span class="badge badge-{{ $badge['variant'] }} text-[10px] mt-2 inline-block">{{ $badge['label'] }}</span>
    @endif
</div>