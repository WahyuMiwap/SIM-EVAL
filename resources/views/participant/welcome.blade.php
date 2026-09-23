@extends('layouts.participant')

@section('title', 'Bergabung — SIM-EVAL P2M')

@section('content')
<div class="welcome-bg relative flex flex-col justify-center items-center" id="welcomeWrapper">

    {{-- Ambient decorative background glow --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-72 sm:w-96 h-72 sm:h-96 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-72 sm:w-96 h-72 sm:h-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    {{-- Main Responsive Card Container --}}
    <div class="w-full max-w-[420px] mx-auto relative z-10 px-1 sm:px-0 animate-fade-in">
        <div class="glass p-5 sm:p-7 md:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none transition-all">

            {{-- Top Brand Header --}}
            <div class="text-center mb-6">
                @if(setting('app.logo'))
                    <img src="{{ setting('app.logo') }}" alt="Logo" class="w-12 h-12 rounded-2xl object-cover mx-auto mb-3 shadow-xs border border-slate-200/70">
                @else
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xs" style="background: var(--primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                @endif
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200/60 dark:border-blue-800/60 text-blue-700 dark:text-blue-300 text-[11px] font-semibold tracking-wide uppercase mb-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></span>
                    {{ setting('app.subnama', 'P2M · BNN Kota Surabaya') }}
                </div>
                <h1 class="font-display font-bold text-xl sm:text-2xl text-slate-900 dark:text-white tracking-tight">Bergabung Sesi</h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-[280px] mx-auto leading-relaxed">
                    Masukkan 6 digit kode PIN kegiatan dari petugas
                </p>
            </div>

            <form id="joinForm" method="POST" action="{{ route('participant.join') }}" class="space-y-4">
                @csrf

                {{-- Kode Join Input --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 text-center mb-2.5">
                        Kode Join (6 Karakter)
                    </label>
                    @php
                        $pre = str_split(str_pad(substr($prefilledKode ?? '', 0, 6), 6, ' '));
                    @endphp
                    <div class="join-code-inputs">
                        @for($i = 0; $i < 6; $i++)
                            @php $c = trim($pre[$i] ?? ''); @endphp
                            <input type="text" maxlength="1" class="join-code-char {{ $c !== '' ? 'has-value' : '' }}" 
                                   data-index="{{ $i }}" id="kode{{ $i }}" value="{{ $c }}"
                                   autocomplete="off" autocapitalize="characters" spellcheck="false" inputmode="text">
                        @endfor
                    </div>
                    <input type="hidden" name="kode_join" id="kodeJoinHidden" value="{{ $prefilledKode ?? '' }}">
                    @error('kode_join')
                        <p class="text-xs text-center mt-2 text-rose-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Event (Muncul otomatis setelah PIN valid terdeteksi) --}}
                <div id="eventInfoBox" class="hidden rounded-2xl p-3.5 bg-emerald-50/90 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 transition-all animate-fade-in">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <span class="inline-block text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Sesi Terverifikasi</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate mt-0.5" id="eventInfoNama">—</p>
                            <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 mt-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate" id="eventInfoLokasi">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kode Error Banner --}}
                <div id="kodeError" class="hidden rounded-xl p-2.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-center animate-fade-in">
                    <p class="text-xs text-rose-600 dark:text-rose-400 font-medium">Kode join tidak dikenal. Tanyakan kode yang benar kepada petugas.</p>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" for="pesertaNama">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" name="nama" id="pesertaNama" 
                               class="form-input w-full pl-10 pr-3.5 py-2.5 sm:py-3 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/90 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="Masukkan nama lengkap Anda"
                               value="{{ old('nama') }}" required autocomplete="name">
                    </div>
                    @error('nama')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kelompok / Kelas --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300" for="pesertaKelas">
                            <span id="kelompokLabel">Kelas / Kelompok</span> <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Pembeda nama sama</span>
                    </div>
                    <div class="relative rounded-xl shadow-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <input type="text" name="kelas" id="pesertaKelas" 
                               class="form-input w-full pl-10 pr-3.5 py-2.5 sm:py-3 text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/90 text-slate-900 dark:text-white placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                               placeholder="cth: XI MIPA 1 / RW 03 / Divisi Umum"
                               value="{{ old('kelas') }}" required maxlength="50">
                    </div>
                    @error('kelas')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" class="btn btn-primary w-full py-3 sm:py-3.5 rounded-xl font-semibold text-sm sm:text-base flex items-center justify-center gap-2 shadow-md shadow-blue-500/20 active:scale-[0.99] transition-all" id="btnJoin">
                    <span>Bergabung Sekarang</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            {{-- Divider --}}
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white dark:bg-slate-900 px-3 text-slate-400 uppercase tracking-wider font-medium">atau</span>
                </div>
            </div>

            {{-- QR Scan Button --}}
            <button type="button" class="btn btn-secondary w-full py-2.5 sm:py-3 rounded-xl text-sm font-semibold flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all" id="btnScanQR">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Pindai QR Code Kegiatan</span>
            </button>

            {{-- QR Scanner Viewport Container --}}
            <div class="mt-3 hidden rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-950 p-2 text-center transition-all animate-fade-in" id="qrScannerContainer">
                <div class="scanner-viewport relative rounded-xl overflow-hidden" style="height: 220px; background: #000;">
                    <video id="qrVideo" class="w-full h-full object-cover" autoplay muted playsinline></video>
                    <div class="scanner-overlay absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="scanner-frame relative border-2 border-blue-400 rounded-2xl" style="width: 70%; aspect-ratio: 1;">
                            <div class="scan-line"></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-slate-300 mt-2 mb-1.5">Arahkan kamera ke QR Code kegiatan</p>
                <button type="button" class="btn btn-secondary btn-sm w-full py-1.5 text-xs text-rose-400 hover:text-rose-300 hover:bg-slate-800 transition-colors" id="btnStopQR">Tutup Kamera</button>
            </div>

            {{-- Footer Security Note --}}
            <div class="text-center mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800/60">
                <p class="text-[11px] text-slate-400 inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Aman & Privat • Tidak perlu membuat akun
                </p>
            </div>

        </div>
    </div>
</div>
@endsection
