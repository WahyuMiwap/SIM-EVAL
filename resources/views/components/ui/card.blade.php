{{-- Card Component - Props: $class = '' --}}
{{-- Slots: header, body, footer --}}
<div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm {{ $class }}">
    @if($header)
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">{{ $header }}</div>
    @endif
    <div class="p-5">{{ $body }}</div>
    @if($footer)
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">{{ $footer }}</div>
    @endif
</div>