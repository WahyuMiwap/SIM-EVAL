{{-- Scanner Viewport Component (Khusus OMR) --}}
{{-- Props: $class = '', $aspectRatio = '4/3' --}}
{{-- Slots: overlay (reticle), placeholder, controls --}}
<div class="relative w-full bg-slate-950 rounded-xl overflow-hidden border-2 border-slate-800 shadow-inner flex items-center justify-center {{ $class }}" style="aspect-ratio: {{ $aspectRatio }};">
    {{-- Video/Canvas Element --}}
    <video id="scannerVideo" class="absolute inset-0 w-full h-full object-cover" playsinline muted></video>
    <canvas id="scannerCanvas" class="absolute inset-0 w-full h-full hidden"></canvas>
    
    {{-- Placeholder State --}}
    <div id="scannerPlaceholder" class="absolute inset-0 flex items-center justify-center text-center p-4 z-10">
        <div>
            <div class="w-14 h-14 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-300">Scanner OMR Siap Aktif</p>
            <p class="text-xs text-slate-500 mt-1 max-w-xs">Arahkan kamera ke lembar jawaban atau gunakan simulasi continuous scanner.</p>
        </div>
    </div>

    {{-- Reticle Frame (Flash hijau 200ms saat scan berhasil) --}}
    <div id="reticleFrame" class="relative z-20 w-3/4 h-3/4 border-2 border-dashed border-white/40 rounded-lg transition-all duration-200 pointer-events-none flex items-center justify-center">
        <span class="text-[11px] text-white/50 tracking-wider uppercase font-semibold">Posisikan Lembar OMR di Kotak Ini</span>
    </div>

    {{-- Sound Indicator --}}
    <div class="absolute top-3 right-3 z-30 flex items-center gap-1.5 px-2 py-1 rounded bg-black/60 text-[11px] text-emerald-400 font-medium">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        Audio Beep: 880Hz Aktif
    </div>

    {{-- Controls Slot --}}
    @if($controls)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 flex flex-wrap items-center gap-2.5">{{ $controls }}</div>
    @endif
</div>