<aside class="sidebar" id="sidebar">
    {{-- Logo & Brand --}}
    <div class="sidebar-logo flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            @if(setting('app.logo'))
                <img src="{{ setting('app.logo') }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover flex-shrink-0 border border-slate-200/70">
            @else
            {{-- Icon: bar chart / evaluation --}}
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--primary);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                    <path d="M3 20h18"/>
                </svg>
            </div>
            @endif
            <div class="sidebar-text flex flex-col justify-center">
                <p class="font-display font-bold text-sm tracking-wide leading-snug" style="color: var(--text-primary);">{{ setting('app.nama') }}</p>
                <p class="text-xs leading-normal mt-0.5" style="color: var(--text-muted);">{{ setting('app.subnama') }}</p>
            </div>
        </div>

        {{-- Tombol Tutup Menu (Khusus Mobile / PWA) --}}
        <button type="button" class="btn btn-secondary btn-icon md-hide" onclick="toggleSidebarMobile()" title="Tutup Menu" style="width: 2rem; height: 2rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <p class="nav-section-label">Operasional</p>

        <a href="{{ route('operator.dashboard') }}"
           class="nav-item {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}"
           id="nav-dashboard" title="Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="{{ route('operator.kegiatan.index') }}"
           class="nav-item {{ request()->routeIs('operator.kegiatan.*') ? 'active' : '' }}"
           id="nav-kegiatan" title="Daftar Kegiatan & Rekap">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="sidebar-text">Kegiatan & Rekap</span>
        </a>

        <a href="{{ route('operator.lokasi.index') }}"
           class="nav-item {{ request()->routeIs('operator.lokasi.*') ? 'active' : '' }}"
           id="nav-lokasi" title="Master Lokasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="sidebar-text">Master Lokasi</span>
        </a>

        <a href="{{ route('operator.bank-soal.index') }}"
           class="nav-item {{ request()->routeIs('operator.bank-soal.*') ? 'active' : '' }}"
           id="nav-banksoal" title="Bank Soal">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <span class="sidebar-text">Bank Soal</span>
        </a>

        {{-- Kelola Staf / Pengguna (Superadmin saja — disembunyikan dari staf/magang) --}}
        @if(($currentRole ?? '') === 'superadmin')
        <p class="nav-section-label" style="margin-top: 1rem;">Administrasi</p>

        <a href="{{ route('operator.staf.index') }}"
           class="nav-item {{ request()->routeIs('operator.staf.*') ? 'active' : '' }}"
           id="nav-staf" title="Tata Kelola Pengguna">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="sidebar-text">Tata Kelola Pengguna</span>
        </a>

        <a href="{{ route('operator.setting.edit') }}"
           class="nav-item {{ request()->routeIs('operator.setting.*') ? 'active' : '' }}"
           id="nav-tampilan" title="Kustomisasi Tampilan">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span class="sidebar-text">Kustomisasi Tampilan</span>
        </a>
        @endif

        <a href="{{ route('operator.profile') }}"
           class="nav-item {{ request()->routeIs('operator.profile') ? 'active' : '' }}"
           id="nav-profil" title="Profil Saya">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="sidebar-text">Profil Saya</span>
        </a>
    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        {{-- Link ke Halaman Peserta --}}
        <a href="{{ route('participant.welcome') }}" class="nav-item" style="color: var(--primary); font-size: 0.8125rem;" title="Preview Peserta" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="sidebar-text">Buka Portal Peserta (PWA)</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="color: var(--danger); width: 100%; font-size: 0.8125rem;" id="nav-logout" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
