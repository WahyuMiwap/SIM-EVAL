<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('app.nama')) — {{ setting('app.nama') }} {{ setting('app.subnama') }}</title>
    <meta name="description" content="Sistem Informasi Monitoring & Evaluasi Pre-Test/Post-Test {{ setting('app.subnama') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIM-EVAL">
    <meta name="theme-color" content="{{ setting('app.warna_primer', '#4361EE') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- FullCalendar via CDN (Chart.js sudah dari npm via app.js — jangan duplikat) --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js" defer></script>
    {{-- Warna primer dinamis (Kustomisasi Tampilan) --}}
    <style>:root { --primary: {{ setting('app.warna_primer') }}; }</style>
</head>
<body>

@php
    // Identitas dari akun login (bukan lagi simulasi peran).
    $authUser = auth()->user();
    $currentRole = $authUser->role ?? 'operator';
    $userName = $authUser->name ?? 'Staf';
    $userEmail = $authUser->email ?? '';
    $userRoleLabel = $currentRole === 'superadmin' ? 'Super Admin'
        : ($currentRole === 'magang' ? 'Anak Magang' : 'Staf Operator');
    $userInit  = strtoupper(substr($userName, 0, 1));
@endphp

{{-- Sidebar --}}
@include('components.sidebar', compact('userName', 'userEmail', 'userInit', 'currentRole', 'userRoleLabel'))

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

{{-- Global Toast Notification Popup --}}
@include('components.toast-notification')

{{-- Sidebar Overlay (mobile) --}}
<div id="sidebarOverlay" class="sidebar-overlay hidden"
    onclick="toggleSidebarMobile(false)" aria-hidden="true"></div>

<script>
    // Pastikan desktop sidebar selalu dalam mode default normal (ada teks)
    try {
        localStorage.removeItem('sidebarMini');
        document.body.classList.remove('sidebar-mini');
    } catch (e) {}

    // ── Mobile / PWA Sidebar Drawer (Hamburger) ───────────────────
    window.toggleSidebarMobile = function(forceState) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) return;
        
        const isOpen = sidebar.classList.contains('open');
        const nextState = typeof forceState === 'boolean' ? forceState : !isOpen;
        
        if (nextState) {
            sidebar.classList.add('open');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('open')) {
                window.toggleSidebarMobile(false);
            }
        }
    });

    // PWA: daftarkan service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
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

@stack('scripts')

</body>
</html>
