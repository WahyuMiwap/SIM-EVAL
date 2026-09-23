{{-- Table Component --}}
{{-- Props: $headers = [], $class = '', $striped = true, $hover = true --}}
{{-- Usage: Manual render rows di parent (untuk inline input) --}}
<div class="overflow-x-auto {{ $class }}">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                @foreach($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
            {{ $slot }}
        </tbody>
    </table>
</div>