@extends('layouts.participant')

@section('title', 'Bergabung — SIM-EVAL P2M')

@section('content')
<div class="welcome-bg" id="welcomeWrapper">

    {{-- Subtle dot pattern already in CSS --}}

    {{-- Main Card --}}
    <div class="glass animate-fade-in" style="width: 100%; max-width: 420px; padding: 2rem; position: relative; z-index: 1;">

        {{-- Top Brand --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background: var(--primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <h1 class="font-display font-bold text-xl" style="color: var(--text-primary);">Bergabung Sesi Evaluasi</h1>
            <p class="text-sm mt-1" style="color: var(--text-muted);">P2M — BNN Kota Surabaya</p>
        </div>

        <form id="joinForm" method="POST" action="{{ route('participant.join') }}">
            @csrf

            {{-- Kode Join --}}
            <div class="form-group">
                <label class="form-label text-center block">Masukkan Kode Join (6 Karakter)</label>
                <div class="join-code-inputs">
                    @for($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" class="join-code-char" data-index="{{ $i }}" id="kode{{ $i }}" autocomplete="off" inputmode="text">
                    @endfor
                </div>
                <input type="hidden" name="kode_join" id="kodeJoinHidden">
                @error('kode_join')
                    <p class="text-xs text-center mt-2" style="color: var(--danger);">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div class="form-group">
                <label class="form-label" for="pesertaNama">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                <input type="text" name="nama" id="pesertaNama" class="form-input"
                    placeholder="Masukkan nama lengkap Anda"
                    value="{{ old('nama') }}" required autocomplete="name">
                @error('nama')
                    <p class="text-xs mt-1.5" style="color: var(--danger);">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sekolah --}}
            <div class="form-group">
                <label class="form-label" for="pesertaSekolah">Asal Sekolah / Instansi <span style="color: var(--danger);">*</span></label>
                <input type="text" name="sekolah" id="pesertaSekolah" class="form-input"
                    placeholder="cth: SMA Negeri 1 Surabaya"
                    value="{{ old('sekolah') }}" required>
                @error('sekolah')
                    <p class="text-xs mt-1.5" style="color: var(--danger);">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary w-full btn-lg mt-1" id="btnJoin">
                Bergabung Sekarang
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8"/>
                </svg>
            </button>
        </form>

        {{-- Divider --}}
        <div class="flex items-center gap-3 my-4">
            <div class="flex-1" style="height: 1px; background: var(--border);"></div>
            <span class="text-xs" style="color: var(--text-muted);">atau scan</span>
            <div class="flex-1" style="height: 1px; background: var(--border);"></div>
        </div>

        {{-- QR Scan --}}
        <button class="btn btn-secondary w-full" id="btnScanQR">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            Scan QR Code
        </button>

        {{-- QR Scanner Container --}}
        <div class="mt-3 hidden" id="qrScannerContainer">
            <div class="scanner-viewport" style="height: 200px;">
                <video id="qrVideo" class="w-full h-full" style="object-fit: cover;" autoplay muted playsinline></video>
                <div class="scanner-overlay">
                    <div class="scanner-frame" style="width: 65%; aspect-ratio: 1;">
                        <div class="scan-line"></div>
                    </div>
                </div>
            </div>
            <button class="btn btn-secondary btn-sm w-full mt-2" id="btnStopQR">Tutup Kamera</button>
        </div>

        {{-- Note --}}
        <p class="text-center text-xs mt-5" style="color: var(--text-muted);">
            Identitas hanya digunakan untuk sesi ini. Tidak perlu buat akun.
        </p>
    </div>
</div>
@endsection
