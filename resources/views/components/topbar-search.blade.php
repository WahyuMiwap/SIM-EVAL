{{--
    Component: Global Searchbar Topbar
    Mencari seluruh fitur, navigasi, data kegiatan, paket bank soal, lokasi binaan, dan aksi cepat.
--}}

<div class="global-search-wrapper" id="globalSearchWrapper">
    {{-- Search Input Box --}}
    <div class="global-search-input-box" id="globalSearchInputBox">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <input 
            type="text" 
            id="globalSearchInput" 
            class="global-search-input" 
            placeholder="Cari fitur, kegiatan, soal, lokasi..." 
            autocomplete="off" 
            spellcheck="false"
            aria-label="Cari fitur atau data"
        />


    </div>

    {{-- Dropdown Results Panel --}}
    <div class="global-search-dropdown hidden" id="globalSearchDropdown">
        {{-- Results Container --}}
        <div class="search-results-list" id="searchResultsList">
            {{-- Dynamically populated --}}
        </div>

        {{-- Footer: result count only --}}
        <div class="search-dropdown-footer">
            <div class="search-footer-count" id="searchResultCount">Semua data &amp; fitur</div>
        </div>
    </div>
</div>



<style>
/* ── Topbar Layout Integration ──────────────────────────────────── */
.topbar-title-wrap {
    flex-shrink: 0;
    min-width: 140px;
}
.topbar-search-col {
    flex: 1;
    display: flex;
    justify-content: center;
    padding: 0 1rem;
    min-width: 0;
}

/* ── Global Search Styles ────────────────────────────────────────── */
.global-search-wrapper {
    position: relative;
    width: 100%;
    max-width: 460px;
    z-index: 50;
}

.global-search-input-box {
    display: flex;
    align-items: center;
    position: relative;
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-full);
    padding: 0 0.875rem;
    height: 38px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.global-search-input-box:focus-within {
    background: var(--surface);
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
}

.search-icon {
    width: 16px;
    height: 16px;
    color: var(--text-muted);
    flex-shrink: 0;
    margin-right: 0.625rem;
    transition: color 0.15s;
}

.global-search-input-box:focus-within .search-icon {
    color: var(--primary);
}

.global-search-input {
    flex: 1;
    min-width: 0;
    border: none;
    outline: none;
    background: transparent;
    color: var(--text-primary);
    font-family: var(--font-sans);
    font-size: 0.8375rem;
    padding: 0;
}

.global-search-input::placeholder {
    color: var(--text-muted);
    font-size: 0.8125rem;
}

.search-clear-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: var(--r-full);
    background: var(--border);
    color: var(--text-secondary);
    border: none;
    cursor: pointer;
    margin-right: 0.375rem;
    transition: background 0.15s, color 0.15s;
}
.search-clear-btn:hover {
    background: var(--border-strong);
    color: var(--text-primary);
}

.search-shortcut-badge {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 0.6875rem;
    color: var(--text-muted);
    flex-shrink: 0;
    user-select: none;
    pointer-events: none;
}

.search-shortcut-badge kbd {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-xs);
    padding: 1px 4px;
    font-family: inherit;
    font-weight: 600;
    line-height: 1.2;
    box-shadow: 0 1px 1px rgba(0,0,0,0.05);
}

/* ── Dropdown Panel ─────────────────────────────────────────────── */
.global-search-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    min-width: 480px;
    max-width: 580px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-xl), 0 20px 35px -10px rgba(0, 0, 0, 0.15);
    z-index: 60;
    overflow: hidden;
    animation: searchDropdownFadeIn 0.15s ease-out;
}

@keyframes searchDropdownFadeIn {
    from { opacity: 0; transform: translate(-50%, -6px); }
    to   { opacity: 1; transform: translate(-50%, 0); }
}

.search-results-list {
    max-height: 380px;
    overflow-y: auto;
    padding: 0.5rem 0.375rem;
    scrollbar-width: thin;
}

/* Category Section Header */
.search-category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem 0.25rem 0.75rem;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-muted);
}

/* Item */
.search-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.625rem 0.75rem;
    border-radius: var(--r-md);
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: background 0.12s, transform 0.12s;
    user-select: none;
}

.search-item:hover,
.search-item.is-active {
    background: var(--primary-light);
}

.search-item-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: var(--r-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.875rem;
}

.search-item-content {
    flex: 1;
    min-width: 0;
}

.search-item-title-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.3;
}

.search-item-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.search-item.is-active .search-item-title {
    color: var(--primary);
}

.search-item-badge {
    font-size: 0.6875rem;
    font-weight: 600;
    padding: 0.125rem 0.45rem;
    border-radius: var(--r-full);
    flex-shrink: 0;
}

.search-item-desc {
    font-size: 0.75rem;
    color: var(--text-secondary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 0.125rem;
}

.search-item-enter {
    color: var(--text-muted);
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.15s;
    flex-shrink: 0;
}

.search-item.is-active .search-item-enter,
.search-item:hover .search-item-enter {
    opacity: 1;
    color: var(--primary);
}

/* Category Badge Colors */
.badge-nav { background: rgba(67, 97, 238, 0.12); color: var(--primary); }
.badge-kegiatan { background: rgba(34, 197, 94, 0.12); color: #16a34a; }
.badge-soal { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
.badge-lokasi { background: rgba(245, 158, 11, 0.12); color: #d97706; }
.badge-aksi { background: rgba(6, 182, 212, 0.12); color: #0891b2; }

/* Icon Background Colors */
.icon-bg-blue    { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
.icon-bg-emerald { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
.icon-bg-purple  { background: rgba(139, 92, 246, 0.1); color: #7c3aed; }
.icon-bg-amber   { background: rgba(245, 158, 11, 0.1); color: #d97706; }
.icon-bg-cyan    { background: rgba(6, 182, 212, 0.1); color: #0891b2; }

/* Highlight Text */
mark.search-highlight {
    background: transparent;
    color: var(--primary);
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 2px;
}

/* Empty State */
.search-empty-state {
    padding: 2.25rem 1rem;
    text-align: center;
}
.search-empty-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 0.75rem auto;
    color: var(--text-muted);
}
.search-empty-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}
.search-empty-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

/* Footer */
.search-dropdown-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.875rem;
    background: var(--surface-2);
    border-top: 1px solid var(--border);
    font-size: 0.7125rem;
    color: var(--text-muted);
}

.search-footer-shortcuts {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.search-footer-shortcuts kbd {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-xs);
    padding: 1px 4px;
    font-size: 0.65rem;
    font-family: inherit;
    color: var(--text-secondary);
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .global-search-wrapper {
        max-width: 100%;
    }
    .global-search-dropdown {
        position: fixed;
        top: 65px;
        left: 12px;
        right: 12px;
        width: auto;
        min-width: 0;
        max-width: none;
        transform: none;
    }
    .search-shortcut-badge {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── DATA REGISTRY: Semua fitur, navigasi, data & aksi SIM-EVAL ──
    const searchData = [
        // 🧭 Fitur & Halaman Navigasi
        {
            id: 'nav-dashboard',
            title: 'Dashboard Utama',
            category: 'Fitur & Navigasi',
            badge: 'Navigasi',
            badgeClass: 'badge-nav',
            iconClass: 'icon-bg-blue',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>`,
            url: "{{ route('operator.dashboard') }}",
            desc: 'Ringkasan statistik evaluasi, grafik N-Gain, dan kegiatan aktif',
            keywords: 'dashboard beranda home statistik grafik ringkasan overview utama'
        },
        {
            id: 'nav-kegiatan',
            title: 'Daftar Kegiatan P2M',
            category: 'Fitur & Navigasi',
            badge: 'Navigasi',
            badgeClass: 'badge-nav',
            iconClass: 'icon-bg-blue',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
            url: "{{ route('operator.kegiatan.index') }}",
            desc: 'Kelola jadwal sosialisasi, evaluasi pre/post test, dan peserta',
            keywords: 'kegiatan sosialisasi jadwal evaluasi daftar event p2m agenda sesi'
        },
        {
            id: 'nav-kegiatan-baru',
            title: 'Tambah Kegiatan Baru',
            category: 'Fitur & Navigasi',
            badge: 'Aksi',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-emerald',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>`,
            url: "{{ route('operator.kegiatan.index') }}?action=create",
            desc: 'Buat agenda sosialisasi P4GN dan jadwalkan evaluasi baru',
            keywords: 'tambah buat kegiatan baru sosialisasi form create add'
        },
        {
            id: 'nav-bank-soal',
            title: 'Bank Soal Pre-Test & Post-Test',
            category: 'Fitur & Navigasi',
            badge: 'Navigasi',
            badgeClass: 'badge-nav',
            iconClass: 'icon-bg-blue',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
            url: "{{ route('operator.bank-soal.index') }}",
            desc: 'Katalog paket soal evaluasi pre-test dan post-test BNN',
            keywords: 'bank soal pertanyaan kuis pretest posttest paket ujian evaluasi tes'
        },
        {
            id: 'nav-soal-baru',
            title: 'Buat Paket Soal Baru',
            category: 'Fitur & Navigasi',
            badge: 'Aksi',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-emerald',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            url: "{{ route('operator.bank-soal.create') }}",
            desc: 'Tambah paket soal evaluasi baru dengan pertanyaan dan kunci jawaban',
            keywords: 'tambah buat paket soal baru create kuis pertanyaan input form'
        },
        {
            id: 'nav-lokasi',
            title: 'Master Lokasi Binaan',
            category: 'Fitur & Navigasi',
            badge: 'Navigasi',
            badgeClass: 'badge-nav',
            iconClass: 'icon-bg-blue',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`,
            url: "{{ route('operator.lokasi.index') }}",
            desc: 'Database sekolah binaan, instansi, lapas, dan kelompok sasaran',
            keywords: 'lokasi master tempat sekolah sman smk lapas instansi database sasaran'
        },
        {
            id: 'nav-profil',
            title: 'Profil Staf Operator',
            category: 'Fitur & Navigasi',
            badge: 'Navigasi',
            badgeClass: 'badge-nav',
            iconClass: 'icon-bg-blue',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
            url: "{{ route('operator.profile') }}",
            desc: 'Informasi akun staf P2M BNN Kota Surabaya & preferensi',
            keywords: 'profil saya akun operator staf biodata settings user foto staf p2m'
        },
        {
            id: 'nav-peserta',
            title: 'Portal Peserta (Mode Ujian)',
            category: 'Fitur & Navigasi',
            badge: 'Portal Peserta',
            badgeClass: 'badge-soal',
            iconClass: 'icon-bg-purple',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`,
            url: "{{ route('participant.welcome') }}",
            desc: 'Tampilan interaktif peserta sosialisasi untuk join tes via PIN',
            keywords: 'peserta murid siswa join pin ujian kuis mobile mode portal'
        },

        // 📋 Data Kegiatan Sosialisasi (Mock)
        {
            id: 'data-kegiatan-1',
            title: 'Sosialisasi Anti Narkoba — SMA N 5 Surabaya',
            category: 'Data Kegiatan',
            badge: 'Kegiatan',
            badgeClass: 'badge-kegiatan',
            iconClass: 'icon-bg-emerald',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`,
            url: "{{ route('operator.kegiatan.detail', 1) }}",
            desc: 'PIN: AB1C2D • Status: Jeda • 42 Peserta • Mode Digital',
            keywords: 'kegiatan sosialisasi sma n 5 surabaya sma5 ab1c2d digital jeda peserta'
        },
        {
            id: 'data-kegiatan-2',
            title: 'Sosialisasi P4GN — SMAN 12 Surabaya',
            category: 'Data Kegiatan',
            badge: 'Kegiatan',
            badgeClass: 'badge-kegiatan',
            iconClass: 'icon-bg-emerald',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`,
            url: "{{ route('operator.kegiatan.detail', 2) }}",
            desc: 'PIN: XY9Z8W • Status: Selesai • 35 Peserta • Mode Kertas',
            keywords: 'kegiatan sosialisasi p4gn sman 12 surabaya sman12 xy9z8w kertas selesai'
        },
        {
            id: 'data-kegiatan-3',
            title: 'Sosialisasi Narkoba — Lapas Kelas I Surabaya',
            category: 'Data Kegiatan',
            badge: 'Kegiatan',
            badgeClass: 'badge-kegiatan',
            iconClass: 'icon-bg-emerald',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`,
            url: "{{ route('operator.kegiatan.detail', 3) }}",
            desc: 'PIN: LP3K4X • Status: Menunggu • 0 Peserta • Mode Kertas',
            keywords: 'kegiatan sosialisasi narkoba lapas kelas 1 i surabaya lp3k4x kertas menunggu'
        },

        // 📝 Data Bank Soal (Mock)
        {
            id: 'data-soal-1',
            title: 'Pre-Test Anti Narkoba Umum',
            category: 'Bank Soal',
            badge: 'Paket Soal',
            badgeClass: 'badge-soal',
            iconClass: 'icon-bg-purple',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
            url: "{{ route('operator.bank-soal.detail', 1) }}",
            desc: '10 Soal • Durasi 30 Menit • Digunakan 5x • Pre-Test',
            keywords: 'paket soal pre-test anti narkoba umum 10 soal 30 menit pretest evaluasi'
        },
        {
            id: 'data-soal-2',
            title: 'Post-Test Anti Narkoba Umum',
            category: 'Bank Soal',
            badge: 'Paket Soal',
            badgeClass: 'badge-soal',
            iconClass: 'icon-bg-purple',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
            url: "{{ route('operator.bank-soal.detail', 2) }}",
            desc: '10 Soal • Durasi 30 Menit • Digunakan 5x • Post-Test',
            keywords: 'paket soal post-test anti narkoba umum 10 soal 30 menit posttest evaluasi'
        },
        {
            id: 'data-soal-3',
            title: 'Pre-Test P4GN Pelajar',
            category: 'Bank Soal',
            badge: 'Paket Soal',
            badgeClass: 'badge-soal',
            iconClass: 'icon-bg-purple',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>`,
            url: "{{ route('operator.bank-soal.detail', 3) }}",
            desc: '8 Soal • Durasi 20 Menit • Digunakan 2x • Sasaran Pelajar',
            keywords: 'paket soal pre-test p4gn pelajar siswa sekolah 8 soal kuis'
        },
        {
            id: 'data-soal-4',
            title: 'Pre-Test Lapas — Khusus',
            category: 'Bank Soal',
            badge: 'Paket Soal',
            badgeClass: 'badge-soal',
            iconClass: 'icon-bg-purple',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>`,
            url: "{{ route('operator.bank-soal.detail', 4) }}",
            desc: '12 Soal • Durasi 25 Menit • Digunakan 1x • Sasaran Lapas',
            keywords: 'paket soal pre-test lapas narapidana khusus 12 soal rutan'
        },
        {
            id: 'data-soal-edit-1',
            title: 'Edit Soal Pre-Test Anti Narkoba',
            category: 'Bank Soal',
            badge: 'Edit Paket',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-amber',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>`,
            url: "{{ route('operator.bank-soal.edit', 1) }}",
            desc: 'Perbarui pertanyaan, opsi pilihan ganda A/B/C/D, dan kunci jawaban',
            keywords: 'edit ubah paket soal pre-test umum kunci jawaban pertanyaan edit'
        },

        // 📍 Data Lokasi Binaan (Mock)
        {
            id: 'data-lokasi-1',
            title: 'SMA N 5 Surabaya',
            category: 'Lokasi Binaan',
            badge: 'Sekolah',
            badgeClass: 'badge-lokasi',
            iconClass: 'icon-bg-amber',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`,
            url: "{{ route('operator.lokasi.index') }}?q=SMA+N+5",
            desc: 'Jl. Pemuda No. 5, Genteng • 5 Kegiatan • Sasaran Sekolah',
            keywords: 'lokasi sma n 5 surabaya genteng sekolah pemuda sma5 binaan'
        },
        {
            id: 'data-lokasi-2',
            title: 'SMAN 12 Surabaya',
            category: 'Lokasi Binaan',
            badge: 'Sekolah',
            badgeClass: 'badge-lokasi',
            iconClass: 'icon-bg-amber',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`,
            url: "{{ route('operator.lokasi.index') }}?q=SMAN+12",
            desc: 'Jl. Semolowaru No. 45, Sukolilo • 3 Kegiatan • Sasaran Sekolah',
            keywords: 'lokasi sman 12 surabaya sukolilo semolowaru sekolah binaan'
        },
        {
            id: 'data-lokasi-3',
            title: 'Lapas Kelas I Surabaya',
            category: 'Lokasi Binaan',
            badge: 'Lapas',
            badgeClass: 'badge-lokasi',
            iconClass: 'icon-bg-amber',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>`,
            url: "{{ route('operator.lokasi.index') }}?q=Lapas",
            desc: 'Jl. Raya Medaeng No. 1, Waru • 2 Kegiatan • Sasaran Lapas',
            keywords: 'lokasi lapas kelas 1 i surabaya medaeng waru penjara rutan binaan'
        },
        {
            id: 'data-lokasi-4',
            title: 'SMA Hang Tuah 1 Surabaya',
            category: 'Lokasi Binaan',
            badge: 'Sekolah',
            badgeClass: 'badge-lokasi',
            iconClass: 'icon-bg-amber',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>`,
            url: "{{ route('operator.lokasi.index') }}?q=Hang+Tuah",
            desc: 'Jl. Balongsari Tama, Asemrowo • 1 Kegiatan • Sasaran Sekolah',
            keywords: 'lokasi sma hang tuah 1 sby asemrowo balongsari sekolah binaan'
        },

        // ⚡ Aksi Cepat
        {
            id: 'action-print-soal',
            title: 'Cetak Lembar Soal A4 (Kertas Ujian)',
            category: 'Aksi Cepat',
            badge: 'Cetak A4',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-cyan',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>`,
            url: "{{ route('operator.bank-soal.detail', 1) }}",
            desc: 'Buka lembar soal resmi BNN format 1 lembar A4 untuk ujian kertas',
            keywords: 'cetak print lembar soal kertas a4 bnn ujian fisik printout'
        },
        {
            id: 'action-toggle-theme',
            title: 'Ganti Mode Gelap / Terang',
            category: 'Aksi Cepat',
            badge: 'Aksi',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-cyan',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>`,
            action: 'toggleTheme',
            desc: 'Beralih instan antara mode tampilan gelap dan terang',
            keywords: 'tema dark mode light gelap terang ganti toggle theme warna'
        },
        {
            id: 'action-reauth',
            title: 'Kunci Layar / Re-Autentikasi Akun',
            category: 'Aksi Cepat',
            badge: 'Keamanan',
            badgeClass: 'badge-aksi',
            iconClass: 'icon-bg-cyan',
            icon: `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>`,
            action: 'openReauthModal',
            desc: 'Buka verifikasi keamanan staf operator dengan password',
            keywords: 'kunci reauth autentikasi password keamanan lock konfirmasi'
        }
    ];

    // Rekomendasi cepat saat searchbar pertama kali di-klik (input kosong)
    const quickRecommendations = [
        'nav-dashboard',
        'nav-bank-soal',
        'nav-soal-baru',
        'nav-kegiatan',
        'action-print-soal'
    ];

    // DOM Elements
    const wrapper   = document.getElementById('globalSearchWrapper');
    const input     = document.getElementById('globalSearchInput');
    const dropdown  = document.getElementById('globalSearchDropdown');
    const list      = document.getElementById('searchResultsList');
    const countEl   = document.getElementById('searchResultCount');

    let currentMatches = [];
    let activeIndex    = -1;

    // ── Helper: Escape regex string ──────────────────────────────────
    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // ── Helper: Highlight matching text ──────────────────────────────
    function highlightText(text, query) {
        if (!query) return text;
        const re = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        return text.replace(re, '<mark class="search-highlight">$1</mark>');
    }

    // ── Helper: Render Search Results ────────────────────────────────
    function renderResults(items, isQuick = false, query = '') {
        currentMatches = items;
        activeIndex = items.length > 0 ? 0 : -1;

        if (items.length === 0) {
            list.innerHTML = `
                <div class="search-empty-state">
                    <svg class="search-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="search-empty-title">Tidak ditemukan hasil</p>
                    <p class="search-empty-desc">Tidak ada fitur atau data yang cocok dengan "<strong>${escapeHtml(query)}</strong>"</p>
                </div>
            `;
            if (countEl) countEl.textContent = '0 hasil';
            return;
        }

        if (countEl) {
            countEl.textContent = isQuick ? 'Rekomendasi Cepat' : `${items.length} hasil ditemukan`;
        }

        // Group by category
        const groups = {};
        items.forEach((item, idx) => {
            const cat = isQuick ? 'Rekomendasi Cepat' : item.category;
            if (!groups[cat]) groups[cat] = [];
            groups[cat].push({ item, globalIndex: idx });
        });

        let html = '';
        for (const [catName, catItems] of Object.entries(groups)) {
            html += `<div class="search-category-header"><span>${escapeHtml(catName)}</span></div>`;
            catItems.forEach(({ item, globalIndex }) => {
                const isActive = globalIndex === activeIndex ? 'is-active' : '';
                const titleHtml = isQuick ? escapeHtml(item.title) : highlightText(escapeHtml(item.title), query);
                const descHtml  = isQuick ? escapeHtml(item.desc)  : highlightText(escapeHtml(item.desc), query);

                html += `
                    <div class="search-item ${isActive}" data-index="${globalIndex}" data-id="${item.id}">
                        <div class="search-item-icon-wrap ${item.iconClass}">
                            ${item.icon}
                        </div>
                        <div class="search-item-content">
                            <div class="search-item-title-row">
                                <span class="search-item-title">${titleHtml}</span>
                                <span class="search-item-badge ${item.badgeClass}">${escapeHtml(item.badge)}</span>
                            </div>
                            <div class="search-item-desc">${descHtml}</div>
                        </div>
                        <div class="search-item-enter">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>
                `;
            });
        }

        list.innerHTML = html;
        bindItemEvents();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Item Click & Hover ───────────────────────────────────────────
    function bindItemEvents() {
        const itemEls = list.querySelectorAll('.search-item');
        itemEls.forEach(el => {
            el.addEventListener('mouseenter', () => {
                const idx = parseInt(el.getAttribute('data-index'), 10);
                setActiveItem(idx);
            });
            el.addEventListener('click', () => {
                const idx = parseInt(el.getAttribute('data-index'), 10);
                triggerItem(currentMatches[idx]);
            });
        });
    }

    function setActiveItem(index) {
        activeIndex = index;
        const itemEls = list.querySelectorAll('.search-item');
        itemEls.forEach((el, idx) => {
            if (idx === index) {
                el.classList.add('is-active');
                el.scrollIntoView({ block: 'nearest' });
            } else {
                el.classList.remove('is-active');
            }
        });
    }

    // ── Execute Item Action / Navigation ─────────────────────────────
    function triggerItem(item) {
        if (!item) return;
        closeSearch();

        if (item.action === 'toggleTheme') {
            if (typeof toggleTheme === 'function') toggleTheme();
            return;
        }

        if (item.action === 'openReauthModal') {
            if (typeof openReauthModal === 'function') {
                openReauthModal('Aksi Keamanan Staf', '#');
            }
            return;
        }

        if (item.url) {
            window.location.href = item.url;
        }
    }

    // ── Search Query Filter ──────────────────────────────────────────
    function performSearch(query) {
        const trimmed = query.trim().toLowerCase();

        if (!trimmed) {
            // Show quick recommendations
            const recs = searchData.filter(item => quickRecommendations.includes(item.id));
            renderResults(recs, true);
            return;
        }

        const words = trimmed.split(/\s+/);
        const matches = searchData.filter(item => {
            const searchable = (item.title + ' ' + item.category + ' ' + item.badge + ' ' + item.desc + ' ' + item.keywords).toLowerCase();
            return words.every(word => searchable.includes(word));
        });

        renderResults(matches, false, trimmed);
    }

    // ── Open / Close Dropdown ────────────────────────────────────────
    function openSearch() {
        dropdown.classList.remove('hidden');
        performSearch(input.value);
    }

    function closeSearch() {
        dropdown.classList.add('hidden');
    }

    // ── Event Listeners ──────────────────────────────────────────────
    input.addEventListener('focus', () => {
        openSearch();
    });

    input.addEventListener('input', () => {
        performSearch(input.value);
    });


    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target) && !dropdown.classList.contains('hidden')) {
            closeSearch();
        }
    });

    // Keyboard navigation inside input
    input.addEventListener('keydown', (e) => {
        if (dropdown.classList.contains('hidden')) {
            if (e.key === 'ArrowDown' || e.key === 'Enter') {
                openSearch();
                e.preventDefault();
            }
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (currentMatches.length > 0) {
                const next = (activeIndex + 1) % currentMatches.length;
                setActiveItem(next);
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentMatches.length > 0) {
                const prev = (activeIndex - 1 + currentMatches.length) % currentMatches.length;
                setActiveItem(prev);
            }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeIndex < currentMatches.length) {
                triggerItem(currentMatches[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeSearch();
        }
    });

    // ESC to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !dropdown.classList.contains('hidden')) {
            e.preventDefault();
            closeSearch();
        }
    });
});
</script>
