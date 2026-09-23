{{-- Tabs Component --}}
{{-- Props: $items = [], $activeId, $wireModel = null, $class = '' --}}
<div class="border-b border-slate-200 dark:border-slate-700 {{ $class }}" role="tablist">
    @foreach($items as $item)
        <button 
            type="button" 
            role="tab" 
            aria-selected="{{ $item['id'] === $activeId ? 'true' : 'false' }}"
            @if($wireModel) wire:click="$set('{{ $wireModel }}', '{{ $item['id'] }}')" @else onclick="document.querySelectorAll('[role=tabpanel]').forEach(p=>p.hidden=true);document.getElementById('{{ $item['id'] }}').hidden=false;this.parentElement.querySelectorAll('[role=tab]').forEach(b=>b.classList.remove('active'));this.classList.add('active')" @endif
            class="method-tab relative px-4 py-2.5 text-xs font-semibold rounded-t-lg transition-all flex items-center gap-2 border-b-2 {{ $item['id'] === $activeId ? 'border-primary text-primary' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white border-transparent' }}"
        >
            @if(isset($item['icon']))
                <span class="w-4 h-4 flex-shrink-0">{!! $item['icon'] !!}</span>
            @endif
            <span>{{ $item['label'] }}</span>
            @if(isset($item['badge']))
                <span class="badge badge-{{ $item['badge']['variant'] }} text-[10px] px-1.5 py-0.2 rounded-full">{{ $item['badge']['label'] }}</span>
            @endif
        </button>
    @endforeach
</div>