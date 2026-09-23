<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Kedaluwarsa (419) — {{ config('app.name', 'SIM-EVAL') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center p-8 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-200 dark:border-amber-800/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 mb-2">
            Error 419 &middot; Sesi Kedaluwarsa
        </span>

        <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1">Halaman Telah Berakhir</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 mb-6 leading-relaxed">
            Halaman ini telah terbuka terlalu lama sehingga token keamanan (*CSRF*) kedaluwarsa. Silakan muat ulang halaman untuk melanjutkan.
        </p>

        <div class="space-y-2.5">
            <a href="{{ route('login') }}" class="btn btn-primary w-full rounded-xl py-2.5 text-xs font-semibold shadow-xs flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Muat Ulang Halaman Login</span>
            </a>
            <button type="button" onclick="window.history.back()" class="btn btn-secondary w-full rounded-xl py-2.5 text-xs font-medium text-slate-600 dark:text-slate-300">
                Kembali ke Halaman Sebelumnya
            </button>
        </div>

        <p class="text-[11px] text-slate-400 mt-5">
            Mengarahkan otomatis dalam <span id="countdown" class="font-bold text-amber-600">3</span> detik...
        </p>
    </div>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            if (countdownEl) countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>
