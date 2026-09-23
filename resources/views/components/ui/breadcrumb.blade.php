{{-- Breadcrumb Component --}}
{{-- Props: $items = [], $class = '' --}}
<nav class="flex items-center gap-2 text-xs text-slate-500 {{ $class }}" aria-label="Breadcrumb">
    @foreach($items as $index => $item)
        @if($index > 0) <span class="mx-1">/</span> @endif
        @if(isset($item['url']) && $index < count($items) - 1)
            <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors">{{ $item['label'] }}</a>
        @else
            <span class="text-slate-700 dark:text-slate-300 font-medium truncate max-w-xs">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>