<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ setting('app.nama', 'SIM-EVAL') }}">
    <meta name="application-name" content="{{ setting('app.nama', 'SIM-EVAL') }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#4361EE">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    <title>@yield('title', 'SIM-EVAL') — Evaluasi P2M</title>
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Participant layout override — minimal chrome & safe area */
        body {
            background: var(--bg);
            min-height: 100vh;
            min-height: 100dvh;
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            padding-left: env(safe-area-inset-left, 0px);
            padding-right: env(safe-area-inset-right, 0px);
        }
    </style>
</head>
<body>
    @yield('content')
    <script>
        // PWA: daftarkan service worker (gagal diam-diam bila tak didukung)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }
    </script>
</body>
</html>
