@extends('layouts.app')

@section('title', 'Riwayat Aktivitas & Jejak Audit')
@section('page-title', 'Riwayat Aktivitas')
@section('page-subtitle', 'Jejak audit aksi sensitif seluruh pengguna sistem SIM-EVAL')

@section('content')

@php
    $actionLabels = [
        'login'            => 'Masuk (Login)',
        'logout'           => 'Keluar (Logout)',
        'tambah_akun'      => 'Tambah Akun',
        'ubah_akun'        => 'Ubah Akun',
        'reset_password'   => 'Reset Kata Sandi',
        'toggle_status'    => 'Ubah Status Akun',
        'hapus_akun'       => 'Hapus Akun',
        'tambah_kegiatan'  => 'Tambah Kegiatan',
        'ubah_kegiatan'    => 'Ubah Kegiatan',
        'ubah_status'      => 'Ubah Status Kegiatan',
        'ubah_fase'        => 'Ubah Fase Kegiatan',
        'hapus_kegiatan'   => 'Hapus Kegiatan',
        'tambah_lokasi'    => 'Tambah Lokasi',
        'ubah_lokasi'      => 'Ubah Lokasi',
        'hapus_lokasi'     => 'Hapus Lokasi',
        'tambah_paket'     => 'Tambah Paket Soal',
        'ubah_paket'       => 'Ubah Paket Soal',
        'hapus_paket'      => 'Hapus Paket Soal',
        'ubah_tampilan'    => 'Kustomisasi Tampilan',
    ];

    $targetLabels = [
        'user'     => 'Pengguna',
        'event'    => 'Kegiatan',
        'location' => 'Lokasi',
        'package'  => 'Paket Soal',
        'setting'  => 'Pengaturan',
    ];

    $roleLabels = [
        'superadmin' => 'Super Admin',
        'operator'   => 'Staf Operator',
        'magang'     => 'Anak Magang',
    ];
@endphp

{{-- ─── 1. Breadcrumb & Navigasi ────────────────────────────────── --}}
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <nav class="flex items-center gap-2 text-xs">
        <a href="{{ route('operator.staf.index') }}" class="text-slate-500 hover:text-primary transition-colors inline-flex items-center gap-1.5 font-medium">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Tata Kelola Pengguna</span>
        </a>
        <span class="text-slate-300 dark:text-slate-600">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Riwayat Aktivitas</span>
    </nav>
    <a href="{{ route('operator.staf.index') }}" class="btn btn-secondary btn-sm rounded-xl inline-flex items-center gap-1.5 text-xs shadow-2xs">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span>Kembali ke Pengguna</span>
    </a>
</div>

{{-- ─── 2. Unified Card Jejak Audit (Soft Rounded-2xl) ─────────── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-visible" style="padding: 0;">

    {{-- Card Header --}}
    <div class="kg-header flex items-center justify-between p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary flex items-center justify-center flex-shrink-0 border border-blue-100 dark:border-blue-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Jejak Audit Aktivitas</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $logs->total() ?? count($logs) }} log aktivitas tercatat &middot; Jejak audit aksi sensitif sistem</p>
            </div>
        </div>
        @if(isset($logs) && method_exists($logs, 'total'))
        <div class="text-xs text-slate-400 hidden sm:block">
            Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
        </div>
        @endif
    </div>

    {{-- ── Filter Bar Soft (Persis Bank Soal) ─────────────────────── --}}
    <div class="kg-filterbar relative z-20 p-3.5 md:p-4 bg-slate-50/50 dark:bg-slate-900/40 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3 flex-wrap">
        
        {{-- Search Input with SVG Icon --}}
        <div class="kg-search-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="kg-search-icon" width="14" height="14" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="filterAktivitas" type="text" class="kg-search-input rounded-xl border border-slate-200/70 dark:border-slate-700"
                   placeholder="Cari aktor atau detail..."
                   value="{{ $search }}">
        </div>

        {{-- Custom Filter Dropdown: Jenis Aksi (Persis Bank Soal) --}}
        <div class="relative z-30" id="dropdownAksiWrapper" style="width: 210px;">
            <button type="button"
                    id="btnDropdownAksi"
                    class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer flex items-center justify-between text-left shadow-2xs"
                    style="height: 38px;">
                <span class="truncate font-medium text-slate-700 dark:text-slate-200" id="dropdownAksiLabel">
                    {{ empty($aksi) ? 'Semua Jenis Aksi' : ($actionLabels[$aksi] ?? ucwords(str_replace('_', ' ', $aksi))) }}
                </span>
                <span class="text-slate-400 flex items-center transition-transform duration-200 flex-shrink-0 ml-1.5" id="dropdownAksiCaret">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </button>

            {{-- Floating Dropdown Card --}}
            <div id="dropdownAksiMenu"
                 class="hidden absolute left-0 z-50 mt-1.5 w-72 max-h-80 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
                
                <button type="button"
                        data-aksi=""
                        data-label="Semua Jenis Aksi"
                        class="dropdown-aksi-item w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group {{ empty($aksi) ? 'bg-blue-50/60 dark:bg-blue-950/40' : '' }}">
                    <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs group-hover:text-primary transition-colors">Semua Jenis Aksi</span>
                    <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Tampilkan seluruh jejak log</span>
                </button>

                @foreach(($aksis ?? []) as $a)
                <button type="button"
                        data-aksi="{{ $a }}"
                        data-label="{{ $actionLabels[$a] ?? ucwords(str_replace('_', ' ', $a)) }}"
                        class="dropdown-aksi-item w-full text-left px-3.5 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer group {{ ($aksi ?? '') === $a ? 'bg-blue-50/60 dark:bg-blue-950/40' : '' }}">
                    <span class="block font-semibold text-slate-800 dark:text-slate-100 text-xs group-hover:text-primary transition-colors">
                        {{ $actionLabels[$a] ?? ucwords(str_replace('_', ' ', $a)) }}
                    </span>
                    <span class="block text-[11px] text-slate-400 font-normal mt-0.5">Jejak aksi {{ strtolower($actionLabels[$a] ?? $a) }}</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Active Filter Chip (Persis Bank Soal) --}}
        <div class="kg-active-chip {{ (empty($aksi) && empty($search)) ? 'hidden' : '' }}" id="activeChip">
            <span id="activeChipText">
                @php
                    $chipLabels = [];
                    if ($aksi) $chipLabels[] = $actionLabels[$aksi] ?? ucwords(str_replace('_', ' ', $aksi));
                    if ($search) $chipLabels[] = '"'.$search.'"';
                @endphp
                {{ implode(' · ', $chipLabels) }}
            </span>
            <button id="btnClearFilter" class="kg-chip-clear" aria-label="Hapus filter" title="Reset filter">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Table Soft --}}
    <div style="overflow-x:auto;">
        <table class="data-table w-full text-left" id="auditTable">
            <thead>
                <tr>
                    <th class="py-3 px-4 w-36">Waktu</th>
                    <th class="py-3 px-4">Aktor</th>
                    <th class="py-3 px-4">Aksi</th>
                    <th class="py-3 px-4">Target</th>
                    <th class="py-3 px-4">Detail Aktivitas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 text-xs">
                @forelse(($logs ?? []) as $log)
                <tr class="hover:bg-blue-50/40 dark:hover:bg-slate-800/40 transition-colors">
                    {{-- Waktu --}}
                    <td class="py-3.5 px-4 whitespace-nowrap">
                        <div class="font-medium text-slate-700 dark:text-slate-200">
                            {{ $log->created_at ? $log->created_at->isoFormat('D MMM Y') : '—' }}
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5 tabular-nums">
                            {{ $log->created_at ? $log->created_at->format('H:i') . ' WIB' : '' }}
                        </div>
                    </td>

                    {{-- Aktor --}}
                    <td class="py-3.5 px-4">
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $log->nama_aktor ?? 'Sistem' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $roleLabels[$log->role_aktor] ?? ($log->role_aktor ? ucfirst($log->role_aktor) : 'Sistem') }}
                            </p>
                        </div>
                    </td>

                    {{-- Aksi (Clean text, no pill/badge box) --}}
                    <td class="py-3.5 px-4 whitespace-nowrap">
                        <span class="font-medium text-slate-700 dark:text-slate-200">
                            {{ $actionLabels[$log->aksi] ?? ucwords(str_replace('_', ' ', $log->aksi)) }}
                        </span>
                    </td>

                    {{-- Target --}}
                    <td class="py-3.5 px-4 whitespace-nowrap">
                        @if($log->target_type)
                            <span class="text-slate-600 dark:text-slate-300 font-medium">
                                {{ $targetLabels[$log->target_type] ?? ucfirst($log->target_type) }}
                            </span>
                            @if($log->target_id)
                                <span class="text-slate-400 tabular-nums">#{{ $log->target_id }}</span>
                            @endif
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>

                    {{-- Detail Aktivitas --}}
                    <td class="py-3.5 px-4">
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $log->detail ?? '—' }}</p>
                        @if($log->ip)
                            <p class="text-[10px] text-slate-400 mt-0.5 tabular-nums">IP: {{ $log->ip }}</p>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-sm text-slate-600 dark:text-slate-300">Belum ada riwayat aktivitas</p>
                        <p class="text-xs text-slate-400 mt-1">Aksi sensitif pengguna (login, manajemen akun, perubahan data) akan tercatat di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($logs) && method_exists($logs, 'hasPages') && $logs->hasPages())
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
        {{ $logs->links() }}
    </div>
    @endif

</div>

{{-- ─── Styles Scoped Standar SIM-EVAL (Persis Bank Soal) ──────── --}}
<style>
/* Filter Bar */
.kg-filterbar {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.75rem 1.375rem;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}

/* Search */
.kg-search-wrap {
    position: relative;
    flex: 0 0 auto;
}
.kg-search-icon {
    position: absolute;
    left: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    color: var(--text-muted);
    pointer-events: none;
}
.kg-search-input {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-xl);
    padding: 0.375rem 0.75rem 0.375rem 2.125rem;
    font-size: 0.8125rem;
    color: var(--text-primary);
    font-family: var(--font-sans);
    outline: none;
    width: 250px;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
}
.kg-search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67,97,238,0.08);
}
.kg-search-input::placeholder { color: var(--text-xmuted); }

/* Active filter chip */
.kg-active-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--primary-light);
    color: var(--primary);
    border-radius: var(--r-full);
    padding: 0.2rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
}
.kg-active-chip.hidden { display: none !important; }
.kg-chip-clear {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    color: var(--primary);
    padding: 0;
    opacity: 0.7;
    transition: opacity 0.15s;
}
.kg-chip-clear:hover { opacity: 1; }
</style>

@endsection

@push('scripts')
<script>
(function () {
    const searchInput        = document.getElementById('filterAktivitas');
    const btnDropdownAksi    = document.getElementById('btnDropdownAksi');
    const dropdownAksiMenu   = document.getElementById('dropdownAksiMenu');
    const dropdownAksiLabel  = document.getElementById('dropdownAksiLabel');
    const dropdownAksiCaret  = document.getElementById('dropdownAksiCaret');
    const dropdownAksiItems  = document.querySelectorAll('.dropdown-aksi-item');

    const activeChip         = document.getElementById('activeChip');
    const activeChipText     = document.getElementById('activeChipText');
    const btnClearFilter     = document.getElementById('btnClearFilter');

    let currentSearch = @json($search ?? '');
    let currentAksi   = @json($aksi ?? '');

    function navigateWithFilters(newAksi, newSearch) {
        const params = new URLSearchParams();
        if (newAksi) params.set('aksi', newAksi);
        if (newSearch) params.set('search', newSearch);
        window.location.href = "{{ route('operator.staf.activity') }}" + (params.toString() ? '?' + params.toString() : '');
    }

    // ── Dropdown Aksi Toggle & Click Outside ────────────────────
    function toggleDropdownAksi(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        if (!dropdownAksiMenu) return;
        const isHidden = dropdownAksiMenu.classList.contains('hidden');
        if (isHidden) {
            dropdownAksiMenu.classList.remove('hidden');
            dropdownAksiCaret?.classList.add('rotate-180');
        } else {
            closeDropdownAksi();
        }
    }

    function closeDropdownAksi() {
        if (dropdownAksiMenu && !dropdownAksiMenu.classList.contains('hidden')) {
            dropdownAksiMenu.classList.add('hidden');
            dropdownAksiCaret?.classList.remove('rotate-180');
        }
    }

    btnDropdownAksi?.addEventListener('click', toggleDropdownAksi);

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#dropdownAksiWrapper')) {
            closeDropdownAksi();
        }
    });

    dropdownAksiItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.stopPropagation();
            const val = this.getAttribute('data-aksi') || '';
            closeDropdownAksi();
            navigateWithFilters(val, searchInput ? searchInput.value.trim() : currentSearch);
        });
    });

    // ── Search Input (Enter key or Debounced) ───────────────────
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                navigateWithFilters(currentAksi, this.value.trim());
            }
        });
    }

    // ── Clear Filter Button ─────────────────────────────────────
    btnClearFilter?.addEventListener('click', function () {
        window.location.href = "{{ route('operator.staf.activity') }}";
    });
})();
</script>
@endpush
