{{-- Empty State Component --}}
{{-- Props: $icon, $title, $description, $class = '' --}}
{{-- Slot: action --}}
<div class="flex flex-col items-center gap-3 p-8 text-center {{ $class }}">
    <div class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{{ $icon }}</svg>
    </div>
    <div>
        <p class="font-semibold text-sm text-slate-700 dark:text-slate-300">{{ $title }}</p>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $description }}</p>
    </div>
    @if($action)
        <div class="mt-2">{{ $action }}</div>
    @endif
</div>