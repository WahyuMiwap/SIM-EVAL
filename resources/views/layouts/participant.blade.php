<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#4361EE">
    <title>@yield('title', 'SIM-EVAL') — Evaluasi P2M</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Participant layout override — minimal chrome */
        body { background: var(--bg); }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
