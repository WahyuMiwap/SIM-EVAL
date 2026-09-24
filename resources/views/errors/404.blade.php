<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidak Ditemukan (404) — {{ config('app.name', 'SIM-EVAL') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center p-8 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl">
        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-slate-200 text-slate-700 mb-2">Error 404 &middot; Tidak Ditemukan</span>
        <h1 class="text-xl font-bold mt-1">Halaman tidak tersedia.</h1>
        <p class="text-xs text-slate-500 mt-2 mb-6 leading-relaxed">Tautan mungkin salah, data telah dihapus, atau kode join tidak valid.</p>
        <div class="space-y-2.5">
            <a href="{{ route('operator.dashboard') }}" class="btn btn-primary w-full rounded-xl py-2.5 text-xs font-semibold flex items-center justify-center">Kembali ke Dashboard</a>
            <a href="{{ route('participant.welcome') }}" class="btn btn-secondary w-full rounded-xl py-2.5 text-xs font-medium flex items-center justify-center">Portal Peserta</a>
        </div>
    </div>
</body>
</html>
