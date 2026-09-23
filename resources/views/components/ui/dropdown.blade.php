{{-- Dropdown Component --}}
{{-- Props: $align = 'right', $width = 'w-72', $class = '' --}}
{{-- Slots: trigger, content --}}
<div class="relative inline-block" x-data="{ open: false }">
    <div @click="open = !open" class="cursor-pointer">{{ $trigger }}</div>
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95"
        @click.outside="open = false"
        class="absolute z-50 mt-2 {{ $width }} bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg {{ $align === 'right' ? 'right-0' : 'left-0' }} {{ $class }}"
    >
        <div class="py-1">{{ $content }}</div>
    </div>
</div>