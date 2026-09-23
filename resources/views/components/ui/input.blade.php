{{-- Input Component --}}
{{-- Props: $type = 'text', $name, $label = null, $value = null, $placeholder = null, $error = null, $icon = null, $class = '', $readonly = false, $disabled = false, $min = null, $max = null, $step = null --}}
@if($label)
    <label for="{{ $name }}" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">{{ $label }}</label>
@endif
<div class="relative">
    @if($icon)
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4">{{ $icon }}</svg>
        </div>
    @endif
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}" 
        placeholder="{{ $placeholder }}" 
        class="w-full bg-white border border-slate-300 dark:border-slate-600 dark:bg-slate-800 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 transition-all focus:border-primary focus:ring-2 focus:ring-primary/10 {{ $icon ? 'pl-10' : 'pl-4' }} pr-4 py-2.5 text-sm {{ $class }}"
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($step !== null) step="{{ $step }}" @endif
        @readonly($readonly) @disabled($disabled)
    >
    @if($error)
        <p class="mt-1.5 text-xs text-danger">{{ $error }}</p>
    @endif
</div>