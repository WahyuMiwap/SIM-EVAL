@extends('layouts.participant')

@section('title', $room === 'pretest' ? 'Waiting Room Pre-Test' : 'Waiting Room Post-Test')

@section('content')
<div class="waiting-room" id="waitingRoom"
    data-kegiatan="{{ $session->kegiatan_id }}"
    data-room="{{ $room }}"
    data-poll-url="{{ route('participant.status', $session->id) }}">

    {{-- Top Bar --}}
    <div style="position: fixed; top: 0; left: 0; right: 0; z-index: 10; padding: 0.875rem 1.25rem; display: flex; align-items: center; justify-between; background: var(--surface); border-bottom: 1px solid var(--border); box-shadow: var(--shadow-xs);">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: var(--primary);">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="white">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="font-display font-bold text-sm" style="color: var(--text-primary);">SIM-EVAL</span>
        </div>
        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full" style="background: var(--success-light); border: 1px solid rgba(34,197,94,0.25);">
            <span class="w-2 h-2 rounded-full" style="background: var(--success); animation: blink 1.5s ease infinite;"></span>
            <span class="text-xs font-semibold" style="color: #15803D;" id="waitingCount">{{ $waitingCount ?? 0 }} bergabung</span>
        </div>
    </div>

    <style>
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
    </style>

    {{-- Center Content --}}
    <div class="text-center" style="max-width: 380px; width: 100%; padding-top: 4rem;">

        {{-- Pulse Icon --}}
        <div class="pulse-ring mx-auto mb-6" style="width: 72px; height: 72px;">
            <div style="width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
                background: {{ $room === 'pretest' ? 'var(--primary)' : 'var(--purple)' }};">
                @if($room === 'pretest')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
        </div>

        {{-- Label Sesi --}}
        @if($room === 'pretest')
            <span class="badge badge-blue" style="margin-bottom: 0.75rem; display: inline-flex;">Ruang Tunggu Pre-Test</span>
        @else
            <span class="badge badge-purple" style="margin-bottom: 0.75rem; display: inline-flex;">Ruang Tunggu Post-Test</span>
        @endif

        <h1 class="font-display font-bold text-xl mt-2" style="color: var(--text-primary);">
            @if($room === 'pretest') Menunggu Sesi Dimulai @else Pre-Test Selesai! @endif
        </h1>

        <p class="text-sm mt-2 mb-6 leading-relaxed" style="color: var(--text-secondary);">
            @if($room === 'pretest')
                Tunggu instruktur membuka sesi <strong>Pre-Test</strong>. Jangan tutup halaman ini.
            @else
                Silakan tunggu instruktur selesai menyampaikan materi, lalu <strong>Post-Test</strong> akan dibuka.
            @endif
        </p>

        {{-- Identitas Card --}}
        <div class="glass-card p-4 mb-5 text-left" style="border-radius: var(--r-md);">
            <p class="text-xs font-bold uppercase tracking-wide mb-2.5" style="color: var(--text-muted);">Identitas Anda</p>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background: var(--primary);">
                    {{ strtoupper(substr($session->nama, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-sm leading-tight" style="color: var(--text-primary);">{{ $session->nama }}</p>
                    <p class="text-xs" style="color: var(--text-muted);">{{ $session->sekolah }}</p>
                </div>
                @if($room === 'posttest')
                    <span class="badge badge-gray">Terkunci</span>
                @endif
            </div>

            @if($room === 'posttest')
            <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                <span class="text-xs" style="color: var(--text-muted);">Nilai Pre-Test:</span>
                <span class="font-bold text-sm" style="color: var(--primary);">{{ $session->skor_pretest ?? '—' }}</span>
            </div>
            @endif
        </div>

        {{-- Loading dots --}}
        <div class="flex items-center justify-center gap-1.5 mb-6">
            @foreach([0, 0.2, 0.4] as $delay)
            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--primary); animation: bounce-dot 1.4s ease-in-out infinite; animation-delay: {{ $delay }}s;"></span>
            @endforeach
        </div>

        <style>
            @keyframes bounce-dot {
                0%,80%,100%{transform:translateY(0);opacity:0.4;}
                40%{transform:translateY(-8px);opacity:1;}
            }
        </style>

        {{-- Kode Join info --}}
        <div class="flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--text-muted);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs" style="color: var(--text-muted);">Kode: <span class="font-mono font-bold" style="color: var(--text-secondary);">{{ $session->kode_join }}</span></span>
        </div>
    </div>

    {{-- Hidden trigger --}}
    <div id="sessionStatusTrigger"
        data-status="{{ $session->kegiatan->status }}"
        data-redirect-url="{{ $room === 'pretest' ? route('participant.quiz', ['session' => $session->id, 'type' => 'pretest']) : route('participant.quiz', ['session' => $session->id, 'type' => 'posttest']) }}">
    </div>
</div>
@endsection
