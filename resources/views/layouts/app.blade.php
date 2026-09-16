<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIM-EVAL') — SIM-EVAL P2M BNN Surabaya</title>
    <meta name="description" content="Sistem Informasi Monitoring & Evaluasi Pre-Test/Post-Test Seksi P2M BNN Kota Surabaya">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- CDN for Chart.js & FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
</head>
<body>

@php
    $userName  = 'Staf P2M Demo';
    $userEmail = 'staf@bnnsurabaya.go.id';
    $userInit  = 'S';
@endphp

{{-- Sidebar --}}
@include('components.sidebar', compact('userName', 'userEmail', 'userInit'))

{{-- Main Wrapper --}}
<div class="main-wrapper" id="mainWrapper">

    <header class="topbar">
        {{-- Mobile Toggle --}}
        <button id="sidebarToggle" class="btn btn-secondary btn-icon md-hide" 
            onclick="toggleSidebarMobile()">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Page Title --}}
        <div class="topbar-title-wrap">
            <h1 class="section-title">@yield('page-title', 'Dashboard')</h1>
            <p class="section-desc">@yield('page-subtitle', 'Selamat datang di SIM-EVAL P2M')</p>
        </div>

        {{-- Global Search Bar (Semua data & fitur) --}}
        <div class="topbar-search-col">
            @include('components.topbar-search')
        </div>

        {{-- Right Actions --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- Theme Toggle --}}
            <button id="themeToggleBtn" class="btn btn-secondary btn-icon" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                <svg id="themeIconLight" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg id="themeIconDark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
        </div>
    </header>

    {{-- Page Content --}}
    <main class="main-content">
        @yield('content')
    </main>

</div>

{{-- Re-auth Modal --}}
@include('components.reauth-modal')

{{-- Sidebar Overlay (mobile) --}}
<div id="sidebarOverlay" class="fixed inset-0 z-40 hidden"
    style="background: rgba(17,24,39,0.4);"
    onclick="document.getElementById('sidebar').classList.remove('open'); this.classList.add('hidden');"></div>

<script>
    // ── Sidebar Mini State Persistence ──────────────────────────
    // Restore state instantly (before paint) to avoid layout flash
    (function restoreSidebarState() {
        if (localStorage.getItem('sidebarMini') === '1') {
            document.documentElement.style.setProperty('--sidebar-transition', 'none');
            document.body.classList.add('sidebar-mini');
            // Re-enable CSS transitions after 2 frames (post-paint)
            requestAnimationFrame(() => requestAnimationFrame(() => {
                document.documentElement.style.removeProperty('--sidebar-transition');
            }));
        }
    })();

    function toggleSidebarMini() {
        document.body.classList.toggle('sidebar-mini');
        const isMini = document.body.classList.contains('sidebar-mini');
        localStorage.setItem('sidebarMini', isMini ? '1' : '0');
    }

    // ── Mobile Sidebar ───────────────────────────────────────────
    function toggleSidebarMobile() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('hidden');
    }

    // ── Theme ────────────────────────────────────────────────────
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            document.getElementById('themeIconLight').classList.remove('hidden');
            document.getElementById('themeIconDark').classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            document.getElementById('themeIconLight').classList.add('hidden');
            document.getElementById('themeIconDark').classList.remove('hidden');
        }
    }

    function toggleTheme() {
        const current = localStorage.getItem('theme') || 'light';
        const next = current === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', next);
        applyTheme(next);
    }

    // Load saved theme immediately
    applyTheme(localStorage.getItem('theme') || 'light');
</script>

</body>
</html>
