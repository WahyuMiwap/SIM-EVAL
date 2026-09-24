@extends('layouts.app')

@section('title', 'Kustomisasi Tampilan')
@section('page-title', 'Kustomisasi Tampilan')
@section('page-subtitle', 'Ubah logo, background login, nama aplikasi, tanpa coding')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
    <div class="lg:col-span-2 glass rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">Identitas & Warna Sistem</h2>
            <p class="text-xs text-slate-400 mt-0.5">Berlaku langsung setelah disimpan. Perubahan tercatat di audit trail.</p>
        </div>

        <form method="POST" action="{{ route('operator.setting.update') }}" enctype="multipart/form-data" class="p-4 md:p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Baris 1: Logo & Background Halaman Login (2 Kolom Simetris) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Logo --}}
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-2" for="f_logo">Logo (foto/gambar)</label>
                    <div class="flex items-start gap-3.5">
                        <div class="w-16 h-16 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden flex-shrink-0" id="logoPreviewBox">
                            @if($current['logo'])
                                <img src="{{ $current['logo'] }}" alt="Logo" class="w-full h-full object-cover" id="logoPreviewImg">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" name="logo" id="f_logo" accept=".png,.jpg,.jpeg,.webp"
                                   class="w-full text-xs text-slate-500 file:mr-2.5 file:px-3 file:py-1.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950/50 dark:file:text-blue-300">
                            <p class="text-[11px] text-slate-400 mt-1.5 leading-snug">PNG/JPG/WebP, maksimal 2 MB. Kosongkan bila tidak ingin mengubah.</p>
                            <p class="text-[11px] text-slate-400 mt-1 leading-snug">Logo ini juga dipakai di kop <strong>lembar soal cetak</strong> (kertas offline). Bila kosong, kertas memakai logo bawaan.</p>
                            @if($current['logo'])
                            <label class="inline-flex items-center gap-1.5 mt-2 text-[11px] text-slate-500 cursor-pointer">
                                <input type="checkbox" name="hapus_logo" value="1" class="accent-rose-500"> Hapus logo (kembali ke ikon bawaan)
                            </label>
                            @endif
                            @error('logo')
                                <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Background Halaman Login --}}
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-2" for="f_bg_login">Background Halaman Login</label>
                    <div class="flex items-start gap-3.5">
                        <div class="w-16 h-16 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden flex-shrink-0 relative" id="bgPreviewBox">
                            @if($current['bg_login'])
                                <img src="{{ $current['bg_login'] }}" alt="Background Login" class="w-full h-full object-cover" id="bgPreviewImg">
                            @else
                                <div class="flex flex-col items-center justify-center text-slate-400 text-[10px]" id="bgDefaultIcon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mb-0.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Default Polos</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input type="file" name="bg_login" id="f_bg_login" accept=".png,.jpg,.jpeg,.webp"
                                   class="w-full text-xs text-slate-500 file:mr-2.5 file:px-3 file:py-1.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-950/50 dark:file:text-blue-300">
                            <p class="text-[11px] text-slate-400 mt-1.5 leading-snug">Disarankan format lanskap (cth: 1920&times;1080 px), PNG/JPG/WebP maksimal 5 MB.</p>
                            @if($current['bg_login'])
                            <label class="inline-flex items-center gap-1.5 mt-2 text-[11px] text-slate-500 cursor-pointer">
                                <input type="checkbox" name="hapus_bg_login" value="1" class="accent-rose-500"> Hapus background login (kembali ke default polos)
                            </label>
                            @endif
                            @error('bg_login')
                                <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Nama Aplikasi + Subnama (2 Kolom) --}}
            <div class="pt-7 md:pt-9 border-t border-slate-100 dark:border-slate-800/80 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-2" for="f_nama">Nama Aplikasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" id="f_nama" required maxlength="50" value="{{ old('nama', $current['nama']) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    @error('nama')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-2" for="f_subnama">Subnama / Instansi</label>
                    <input type="text" name="subnama" id="f_subnama" maxlength="100" value="{{ old('subnama', $current['subnama']) }}"
                           placeholder="cth: P2M BNN Kota Surabaya"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
            </div>

            {{-- Baris 3: Warna Primer --}}
            <div class="pt-1">
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-2.5">Warna Primer</label>
                <div class="flex items-center gap-2.5 flex-wrap" id="warnaPresets">
                    @foreach($preset as $w)
                        <button type="button" data-warna="{{ $w }}" title="{{ $w }}"
                                class="w-9 h-9 rounded-xl border-2 transition-all {{ strtoupper(old('warna_primer', $current['warna'])) === $w ? 'border-slate-800 dark:border-white scale-110' : 'border-transparent' }}"
                                style="background: {{ $w }};"></button>
                    @endforeach
                    <label class="inline-flex items-center gap-2 ml-1 text-[11px] text-slate-500 cursor-pointer">
                        <input type="color" name="warna_primer" id="f_warna" value="{{ old('warna_primer', $current['warna']) }}" class="w-9 h-9 rounded-xl cursor-pointer bg-transparent border border-slate-200 dark:border-slate-700 p-0.5">
                        Kustom
                    </label>
                </div>
                @error('warna_primer')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Baris 4: Aksi Simpan & Batal --}}
            <div class="pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('operator.dashboard') }}" class="btn btn-secondary btn-sm rounded-xl px-4 py-2 text-xs">Batal</a>
                <button type="submit" class="btn btn-primary btn-sm rounded-xl px-5 py-2 text-xs font-semibold shadow-xs">Simpan Tampilan</button>
            </div>
        </form>
    </div>

    {{-- Pratinjau langsung --}}
    <div class="space-y-4 lg:sticky lg:top-4">
        {{-- Pratinjau Brand Header --}}
        <div class="glass p-4 md:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100 mb-1">Pratinjau Identitas</h3>
            <p class="text-[11px] text-slate-400 mb-3">Tampilan header/navbar sistem.</p>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 p-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700" id="pvBox" style="background: var(--primary);">
                    <span id="pvLogoSlot"></span>
                </div>
                <div class="min-w-0 flex flex-col justify-center">
                    <p class="font-bold text-sm tracking-wide leading-snug truncate" style="color: var(--text-primary);" id="pvNama">{{ $current['nama'] }}</p>
                    <p class="text-xs leading-normal truncate mt-0.5" style="color: var(--text-muted);" id="pvSubnama">{{ $current['subnama'] }}</p>
                </div>
            </div>
        </div>

        {{-- Pratinjau Mini Login Glassmorphism --}}
        <div class="glass p-4 md:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100">Pratinjau Halaman Login</h3>
                <span class="text-[10px] px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 font-medium">Glassmorphism</span>
            </div>
            <p class="text-[11px] text-slate-400 mb-3">Simulasi tampilan form login dengan background kustom.</p>

            <div class="relative w-full h-44 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 flex items-center justify-center bg-slate-100 dark:bg-slate-950" id="pvLoginBg" style="{{ $current['bg_login'] ? 'background-image: url(' . asset($current['bg_login']) . '); background-size: cover; background-position: center;' : '' }}">
                {{-- Ambient glow bila background polos --}}
                <div id="pvAmbientGlow" class="{{ $current['bg_login'] ? 'hidden' : '' }} absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-8 -left-8 w-24 h-24 rounded-full bg-blue-400/30 blur-xl"></div>
                    <div class="absolute -bottom-8 -right-8 w-24 h-24 rounded-full bg-sky-300/30 blur-xl"></div>
                </div>

                {{-- Overlay gelap jika ada gambar --}}
                <div id="pvDarkOverlay" class="{{ $current['bg_login'] ? '' : 'hidden' }} absolute inset-0 bg-slate-900/40 backdrop-blur-[1px]"></div>

                {{-- Mini Card Glass --}}
                <div class="relative z-10 w-48 p-3 rounded-xl border border-white/80 shadow-md text-center" style="background: rgba(255,255,255,0.82); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <div class="flex items-center justify-center gap-1.5 mb-1.5">
                        <div class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] text-white font-bold flex-shrink-0" id="pvMiniLogo" style="background: var(--primary);">
                            <span id="pvMiniLogoSlot">
                                @if($current['logo'])
                                    <img src="{{ $current['logo'] }}" alt="Logo" class="w-full h-full object-cover rounded-md">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                @endif
                            </span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-800 truncate" id="pvMiniTitle">{{ $current['nama'] }}</span>
                    </div>
                    <div class="w-full h-2 rounded bg-slate-200/80 mb-1.5"></div>
                    <div class="w-full h-2 rounded bg-slate-200/80 mb-2.5"></div>
                    <div class="w-full py-1 rounded-md text-[10px] font-semibold text-white shadow-xs" id="pvMiniBtn" style="background: var(--primary);">
                        Masuk
                    </div>
                </div>
            </div>
        </div>
        {{-- Pratinjau Kop Kertas (lembar soal cetak) --}}
        <div class="glass p-4 md:p-5 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs">
            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100 mb-1">Pratinjau Kop Kertas</h3>
            <p class="text-[11px] text-slate-400 mb-3">Logo yang tampil di kop lembar soal cetak (offline).</p>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white p-4 flex items-start gap-3">
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center overflow-hidden" id="pvPaperLogo">
                    @if($current['logo'])
                        <img src="{{ $current['logo'] }}" alt="Logo kertas" class="w-14 h-14 object-contain">
                    @else
                        <span class="text-[10px] font-bold text-slate-400 border border-slate-300 rounded-full w-14 h-14 flex items-center justify-center">BNN</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold text-slate-800 leading-snug">[NAMA PAKET SOAL]</p>
                    <p class="text-[10px] text-slate-500 leading-snug mt-0.5">NAMA : ....................</p>
                    <p class="text-[10px] text-slate-500 leading-snug">ASAL SEKOLAH : ....................</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const nama = document.getElementById('f_nama');
    const sub = document.getElementById('f_subnama');
    const warna = document.getElementById('f_warna');
    const pvNama = document.getElementById('pvNama');
    const pvSub = document.getElementById('pvSubnama');
    const pvBox = document.getElementById('pvBox');
    const pvSlot = document.getElementById('pvLogoSlot');
    const logoInput = document.getElementById('f_logo');
    const box = document.getElementById('logoPreviewBox');

    const bgInput = document.getElementById('f_bg_login');
    const bgBox = document.getElementById('bgPreviewBox');
    const pvLoginBg = document.getElementById('pvLoginBg');
    const pvAmbientGlow = document.getElementById('pvAmbientGlow');
    const pvDarkOverlay = document.getElementById('pvDarkOverlay');
    const pvMiniLogo = document.getElementById('pvMiniLogo');
    const pvMiniLogoSlot = document.getElementById('pvMiniLogoSlot');
    const pvMiniTitle = document.getElementById('pvMiniTitle');
    const pvMiniBtn = document.getElementById('pvMiniBtn');

    document.querySelectorAll('#warnaPresets button[data-warna]').forEach(btn => {
        btn.addEventListener('click', () => {
            warna.value = btn.dataset.warna;
            document.querySelectorAll('#warnaPresets button[data-warna]').forEach(b => {
                b.classList.remove('border-slate-800', 'dark:border-white', 'scale-110');
                b.classList.add('border-transparent');
            });
            btn.classList.add('border-slate-800', 'dark:border-white', 'scale-110');
            btn.classList.remove('border-transparent');
            applyPreview();
        });
    });

    function applyPreview() {
        const n = nama?.value || 'SIM-EVAL';
        const s = sub?.value || '';
        const w = warna?.value || '#4361EE';

        if (pvNama) pvNama.textContent = n;
        if (pvSub) pvSub.textContent = s;
        if (pvBox) pvBox.style.background = w;
        if (pvMiniTitle) pvMiniTitle.textContent = n;
        if (pvMiniLogo) pvMiniLogo.style.background = w;
        if (pvMiniBtn) pvMiniBtn.style.background = w;
    }

    [nama, sub].forEach(el => el?.addEventListener('input', applyPreview));
    warna?.addEventListener('input', applyPreview);

    logoInput?.addEventListener('change', () => {
        const file = logoInput.files && logoInput.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        if (box) box.innerHTML = `<img src="${url}" alt="Logo" class="w-full h-full object-cover">`;
        if (pvSlot) pvSlot.innerHTML = `<img src="${url}" alt="Logo" class="w-10 h-10 rounded-xl object-cover">`;
        if (pvMiniLogoSlot) pvMiniLogoSlot.innerHTML = `<img src="${url}" alt="Logo" class="w-full h-full object-cover rounded-md">`;
        const pvPaper = document.getElementById('pvPaperLogo');
        if (pvPaper) pvPaper.innerHTML = `<img src="${url}" alt="Logo kertas" class="w-14 h-14 object-contain">`;
        applyPreview();
    });

    bgInput?.addEventListener('change', () => {
        const file = bgInput.files && bgInput.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        if (bgBox) {
            bgBox.innerHTML = `<img src="${url}" alt="Background Login" class="w-full h-full object-cover">`;
        }
        if (pvLoginBg) {
            pvLoginBg.style.backgroundImage = `url('${url}')`;
            pvLoginBg.style.backgroundSize = 'cover';
            pvLoginBg.style.backgroundPosition = 'center';
        }
        if (pvDarkOverlay) pvDarkOverlay.classList.remove('hidden');
        if (pvAmbientGlow) pvAmbientGlow.classList.add('hidden');
    });

    // Pratinjau awal memakai logo tersimpan bila ada
    @if($current['logo'])
    if (pvSlot) pvSlot.innerHTML = `<img src="{{ $current['logo'] }}" alt="Logo" class="w-10 h-10 rounded-xl object-cover">`;
    @endif
    applyPreview();
})();
</script>
@endpush

@endsection
