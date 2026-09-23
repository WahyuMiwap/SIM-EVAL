{{-- Toast Notification Pop-Up (Auto-dismiss 5s dengan tombol X) --}}
@php
    $toastTypes = [
        'success' => [
            'title'  => 'Berhasil!',
            'tag'    => 'Tersimpan',
            'border' => 'border-emerald-200 dark:border-emerald-800/80',
            'iconBg' => 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-100 dark:border-emerald-900/60 text-emerald-600 dark:text-emerald-400',
            'icon'   => 'M5 13l4 4L19 7',
            'tagCls' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
            'barBg'  => 'bg-emerald-100/50 dark:bg-emerald-950/40',
            'barFg'  => 'bg-emerald-500',
        ],
        'warning' => [
            'title'  => 'Perhatian!',
            'tag'    => 'Perlu dicek',
            'border' => 'border-amber-200 dark:border-amber-800/80',
            'iconBg' => 'bg-amber-50 dark:bg-amber-950/60 border-amber-100 dark:border-amber-900/60 text-amber-600 dark:text-amber-400',
            'icon'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            'tagCls' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
            'barBg'  => 'bg-amber-100/50 dark:bg-amber-950/40',
            'barFg'  => 'bg-amber-500',
        ],
        'error' => [
            'title'  => 'Gagal!',
            'tag'    => 'Error',
            'border' => 'border-red-200 dark:border-red-800/80',
            'iconBg' => 'bg-red-50 dark:bg-red-950/60 border-red-100 dark:border-red-900/60 text-red-600 dark:text-red-400',
            'icon'   => 'M6 18L18 6M6 6l12 12',
            'tagCls' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
            'barBg'  => 'bg-red-100/50 dark:bg-red-950/40',
            'barFg'  => 'bg-red-500',
        ],
    ];
    $activeToast = null;
    $activeToastType = null;
    foreach ($toastTypes as $t => $cfg) {
        if (session($t)) { $activeToastType = $t; $activeToast = $cfg; break; }
    }
@endphp
@if($activeToast)
<div id="globalToastPopup"
     class="fixed top-5 right-5 z-[9999] flex flex-col w-[92vw] sm:w-[420px] max-w-full bg-white dark:bg-slate-900 border {{ $activeToast['border'] }} rounded-2xl shadow-xl overflow-hidden transition-all duration-300 ease-out transform translate-y-0 opacity-100 animate-slide-in-right"
     role="alert"
     aria-live="assertive">
    <div class="p-4 flex items-start gap-3.5">
        <div class="w-9 h-9 rounded-xl {{ $activeToast['iconBg'] }} border flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $activeToast['icon'] }}"/>
            </svg>
        </div>

        {{-- Content --}}
        <div class="flex-1 pr-1 min-w-0">
            <div class="flex items-center gap-2">
                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $activeToast['title'] }}</h4>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold {{ $activeToast['tagCls'] }}">{{ $activeToast['tag'] }}</span>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed break-words font-medium">
                {{ session($activeToastType) }}
            </p>
        </div>

        {{-- Tombol X (Tutup Langsung) --}}
        <button type="button"
                id="toastCloseBtn"
                onclick="closeToastPopup()"
                class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0 -mt-1 -mr-1 cursor-pointer"
                title="Tutup Notifikasi">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Indikator Waktu Mundur 5 Detik --}}
    <div class="w-full {{ $activeToast['barBg'] }} h-1 overflow-hidden">
        <div id="toastProgressBar"
             class="{{ $activeToast['barFg'] }} h-full w-full origin-left"
             style="animation: toastCountdown 5s linear forwards;"></div>
    </div>
</div>

<style>
@keyframes toastCountdown {
    from { width: 100%; }
    to { width: 0%; }
}
@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateY(-16px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.animate-slide-in-right {
    animation: slideInRight 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>

<script>
(function() {
    const toast = document.getElementById('globalToastPopup');
    if (!toast) return;

    let timeoutId = setTimeout(() => {
        closeToastPopup();
    }, 5000);

    window.closeToastPopup = function() {
        if (!toast) return;
        clearTimeout(timeoutId);
        toast.classList.add('opacity-0', '-translate-y-3', 'scale-95', 'pointer-events-none');
        setTimeout(() => {
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    };

    // Jeda timer jika kursor diarahkan ke pop-up
    toast.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
        const bar = document.getElementById('toastProgressBar');
        if (bar) bar.style.animationPlayState = 'paused';
    });

    toast.addEventListener('mouseleave', () => {
        const bar = document.getElementById('toastProgressBar');
        if (bar) bar.style.animationPlayState = 'running';
        timeoutId = setTimeout(() => {
            closeToastPopup();
        }, 2000); // 2 detik toleransi setelah kursor pergi
    });
})();
</script>
@endif
