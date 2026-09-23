{{--
    Custom Select Dropdown — Desain Soft Minimalist seragam dengan Lokasi Combobox.
    Menggantikan <select> native dengan trigger button elegan + floating list card,
    tetapi tetap menyertakan <input type="hidden"> agar submit form & relasi data 100% aman.

    Props:
      $name        Nama input form (wajib, misal 'pretest_package_id')
      $id          ID trigger element (opsional)
      $selected    Nilai id terpilih awal (old() / default)
      $options     Array/Collection opsi:
                   [ ['id' => 1, 'label' => '...', 'badge' => '10 Soal', 'type' => 'pretest', 'desc' => '...'], ... ]
      $placeholder Teks ketika belum ada pilihan
      $required    Boolean
--}}
@props([
    'name',
    'id' => null,
    'selected' => null,
    'options' => [],
    'placeholder' => 'Pilih opsi…',
    'required' => false,
])

@php
    $elementId = $id ?? 'custom_sel_' . $name;
    $optionsCollection = collect($options);
    $currentVal = (string)($selected ?? '');
    
    // Cari opsi yang terpilih saat ini
    $selectedOption = $optionsCollection->first(function($opt) use ($currentVal) {
        $val = is_object($opt) ? ($opt->id ?? $opt->value ?? '') : ($opt['id'] ?? $opt['value'] ?? '');
        return (string)$val === $currentVal;
    });

    // Label & badge default jika ada pilihan
    $initialLabel = '';
    $initialBadge = '';
    if ($selectedOption) {
        $initialLabel = is_object($selectedOption) 
            ? ($selectedOption->nama_paket ?? $selectedOption->label ?? $selectedOption->name ?? '') 
            : ($selectedOption['nama_paket'] ?? $selectedOption['label'] ?? $selectedOption['name'] ?? '');
        
        $badgeCount = is_object($selectedOption) 
            ? ($selectedOption->soal_count ?? $selectedOption->badge ?? null) 
            : ($selectedOption['soal_count'] ?? $selectedOption['badge'] ?? null);
            
        if ($badgeCount) {
            $initialBadge = is_numeric($badgeCount) ? $badgeCount . ' Soal' : $badgeCount;
        }
    }
@endphp

<div class="custom-select-component relative" data-custom-select data-name="{{ $name }}">
    {{-- Hidden input untuk form submit --}}
    <input type="hidden" name="{{ $name }}" value="{{ $currentVal }}" @if($required) required @endif data-select-hidden>

    {{-- Trigger Button --}}
    <div class="relative">
        <button type="button"
                id="{{ $elementId }}"
                role="combobox"
                aria-haspopup="listbox"
                aria-expanded="false"
                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-9 cursor-pointer flex items-center justify-between text-left shadow-xs"
                data-select-trigger>
            <span class="truncate min-w-0 flex items-center gap-2" data-select-display>
                @if($initialLabel)
                    <span class="font-medium text-slate-700 dark:text-slate-200 truncate">{{ $initialLabel }}</span>
                    @if($initialBadge)
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">
                            {{ $initialBadge }}
                        </span>
                    @endif
                @else
                    <span class="text-slate-400">{{ $placeholder }}</span>
                @endif
            </span>
            <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center transition-transform duration-200" data-select-caret>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </button>
    </div>

    {{-- Floating Dropdown Card --}}
    <div data-select-dropdown
         class="hidden absolute z-30 mt-1.5 w-full max-h-60 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
        @forelse($optionsCollection as $opt)
            @php
                $optVal = is_object($opt) ? ($opt->id ?? $opt->value ?? '') : ($opt['id'] ?? $opt['value'] ?? '');
                $optLabel = is_object($opt) 
                    ? ($opt->nama_paket ?? $opt->label ?? $opt->name ?? '') 
                    : ($opt['nama_paket'] ?? $opt['label'] ?? $opt['name'] ?? '');
                $optCount = is_object($opt) 
                    ? ($opt->soal_count ?? $opt->badge ?? null) 
                    : ($opt['soal_count'] ?? $opt['badge'] ?? null);
                $optBadge = $optCount ? (is_numeric($optCount) ? $optCount . ' Soal' : $optCount) : null;
                $optType = is_object($opt) ? ($opt->tipe ?? null) : ($opt['tipe'] ?? null);
                $optDesc = is_object($opt) ? ($opt->deskripsi ?? null) : ($opt['deskripsi'] ?? null);
                $isSelected = (string)$optVal === $currentVal;
            @endphp
            <button type="button"
                    data-value="{{ $optVal }}"
                    data-label="{{ $optLabel }}"
                    data-badge="{{ $optBadge ?? '' }}"
                    class="w-full text-left px-3.5 py-2.5 flex items-center justify-between gap-2.5 transition-colors cursor-pointer group {{ $isSelected ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-semibold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-200' }}"
                    data-select-item>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <span class="truncate block text-xs">{{ $optLabel }}</span>
                        @if($isSelected)
                            <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </div>
                    @if($optDesc)
                        <span class="block text-[10.5px] text-slate-400 font-normal truncate mt-0.5">{{ $optDesc }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($optType)
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            {{ $optType }}
                        </span>
                    @endif
                    @if($optBadge)
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">
                            {{ $optBadge }}
                        </span>
                    @endif
                </div>
            </button>
        @empty
            <div class="px-3.5 py-3 text-center text-slate-400 text-xs">
                Tidak ada pilihan tersedia
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
(function () {
    if (window.__customSelectInit) return;
    window.__customSelectInit = true;

    document.querySelectorAll('[data-custom-select]').forEach(root => {
        const trigger = root.querySelector('[data-select-trigger]');
        const dropdown = root.querySelector('[data-select-dropdown]');
        const hiddenInput = root.querySelector('[data-select-hidden]');
        const display = root.querySelector('[data-select-display]');
        const caret = root.querySelector('[data-select-caret]');
        const items = root.querySelectorAll('[data-select-item]');

        function open() {
            // Tutup dropdown lain yang sedang terbuka
            document.querySelectorAll('[data-select-dropdown]').forEach(d => {
                if (d !== dropdown) {
                    d.classList.add('hidden');
                    const otherRoot = d.closest('[data-custom-select]');
                    if (otherRoot) {
                        otherRoot.classList.remove('z-40');
                        const otherCaret = otherRoot.querySelector('[data-select-caret]');
                        if (otherCaret) otherCaret.classList.remove('rotate-180');
                        const otherTrigger = otherRoot.querySelector('[data-select-trigger]');
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            dropdown.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
            root.classList.add('z-40');
            if (caret) caret.classList.add('rotate-180');
        }

        function close() {
            dropdown.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
            root.classList.remove('z-40');
            if (caret) caret.classList.remove('rotate-180');
        }

        function toggle() {
            if (dropdown.classList.contains('hidden')) {
                open();
            } else {
                close();
            }
        }

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            toggle();
        });

        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const val = item.dataset.value;
                const label = item.dataset.label;
                const badge = item.dataset.badge;

                hiddenInput.value = val;
                
                // Update display HTML
                let badgeHtml = '';
                if (badge) {
                    badgeHtml = `<span class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">${badge}</span>`;
                }
                display.innerHTML = `<span class="font-medium text-slate-700 dark:text-slate-200 truncate">${label}</span>${badgeHtml}`;

                // Update visual checkmarks & active states
                items.forEach(i => {
                    const check = i.querySelector('svg');
                    if (i === item) {
                        i.classList.add('bg-blue-50/70', 'dark:bg-blue-950/40', 'text-blue-700', 'dark:text-blue-300', 'font-semibold');
                        i.classList.remove('hover:bg-slate-50', 'text-slate-700');
                        if (!check) {
                            const nameWrapper = i.querySelector('.flex.items-center');
                            if (nameWrapper) {
                                nameWrapper.insertAdjacentHTML('beforeend', '<svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>');
                            }
                        }
                    } else {
                        i.classList.remove('bg-blue-50/70', 'dark:bg-blue-950/40', 'text-blue-700', 'dark:text-blue-300', 'font-semibold');
                        i.classList.add('hover:bg-slate-50', 'text-slate-700');
                        if (check) check.remove();
                    }
                });

                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                close();
            });
        });

        // Click outside closes
        document.addEventListener('click', (e) => {
            if (!root.contains(e.target)) {
                close();
            }
        });

        // Keydown support
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                open();
            } else if (e.key === 'Escape') {
                close();
            }
        });
    });
})();
</script>
@endpush
