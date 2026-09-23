@extends('layouts.app')

@section('title', 'Detail Paket Soal — ' . ($paket->nama_paket ?? 'Bank Soal'))
@section('page-title', 'Detail Paket Soal')
@section('page-subtitle', 'Kelola butir pertanyaan dan kunci jawaban instrumen tes P2M')

@section('content'){{-- ── Breadcrumb ────────────────────────────────────────────── --}}
<div class="flex items-center gap-2 text-xs mb-4 text-slate-400">
    <a href="{{ route('operator.bank-soal.index') }}" class="inline-flex items-center gap-1 font-medium text-slate-500 hover:text-primary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Bank Soal</span>
    </a>
    <span class="text-slate-300 dark:text-slate-600">/</span>
    <span class="font-semibold text-slate-700 dark:text-slate-200 truncate max-w-[360px]">{{ $paket->nama_paket }}</span>
</div>

{{-- ── Header Card ────────────────────────────────────────────── --}}
<div class="glass mb-6 rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs p-5 md:p-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        {{-- Left: Icon + Title & Clean Metadata --}}
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-primary border border-blue-100 dark:border-blue-900/50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg md:text-xl font-bold text-slate-800 dark:text-slate-100 tracking-tight leading-snug">
                    {{ $paket->nama_paket }}
                </h2>
                
                {{-- Clean Metadata Row (Rapi, Tenang, Tidak Ramai) --}}
                <div class="flex items-center gap-2.5 text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex-wrap">
                    <span class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ count($soalList) }} Soal
                    </span>
                    @if(!empty($paket->tema))
                        <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                        <span>{{ $paket->tema }}</span>
                    @endif

                    @if(($dipakai ?? 0) > 0)
                        <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                        <span title="Paket ini dipakai {{ $dipakai }} kegiatan">Dipakai {{ $dipakai }} kegiatan</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Clean Actions (Cetak dan Edit) --}}
        <div class="flex items-center gap-2 flex-wrap lg:flex-nowrap flex-shrink-0">
            <button type="button" id="btnPreviewSheet" class="btn btn-secondary btn-sm rounded-xl shadow-xs inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>Pratinjau Lembar</span>
            </button>
            <button type="button" class="btn btn-secondary btn-sm rounded-xl shadow-xs inline-flex items-center gap-1.5" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Soal Kertas</span>
            </button>

            <a href="{{ route('operator.bank-soal.edit', $paket->id) }}" class="btn btn-primary btn-sm rounded-xl shadow-xs inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Paket Soal</span>
            </a>
        </div>
    </div>
</div>

{{-- ── Questions Container ─────────────────────────────────────── --}}
<div class="glass rounded-2xl border border-slate-200/70 dark:border-slate-800 shadow-xs p-5 md:p-6 mb-6">
    <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
        <div>
            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-100">
                Daftar Butir Pertanyaan
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Kunci jawaban yang benar ditandai warna hijau untuk panduan operator evaluasi.
            </p>
        </div>
        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary bg-blue-50/70 dark:bg-blue-950/40 px-2.5 py-1 rounded-lg border border-blue-100 dark:border-blue-900/40">
            <span>Total {{ count($soalList) }} Soal</span>
        </div>
    </div>

    <div class="space-y-3.5">
        @forelse ($soalList as $idx => $item)
        <div class="rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/30 p-4 transition-colors hover:border-blue-200 dark:hover:border-blue-900/50">
            <div class="flex items-start gap-3 mb-3">
                <span class="w-6 h-6 rounded-lg bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                    {{ $idx + 1 }}
                </span>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 leading-relaxed flex-1">
                    {{ $item->pertanyaan }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 sm:pl-9">
                @foreach (['A','B','C','D'] as $k)
                @php $isKey = ($item->kunci === $k); $txt = $item->opsi[$k] ?? '—'; @endphp
                <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs transition-all {{ $isKey ? 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-300 dark:border-emerald-800/60 font-semibold text-emerald-900 dark:text-emerald-200' : 'bg-white dark:bg-slate-900 border border-slate-200/70 dark:border-slate-800 text-slate-600 dark:text-slate-300' }}">
                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[11px] font-bold flex-shrink-0 {{ $isKey ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400' }}">
                        {{ $k }}
                    </span>
                    <span class="flex-1 truncate leading-snug">{{ $txt }}</span>
                    @if($isKey)
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-100/70 dark:bg-emerald-900/50 px-1.5 py-0.5 rounded-full flex-shrink-0 ml-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Kunci Jawaban
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-center py-12 text-sm text-slate-400">Belum ada butir soal. Klik <strong>Edit Paket Soal</strong> untuk menambahkan.</p>
        @endforelse
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     PRINT SHEET — lembar gabungan soal + kotak jawaban (1 lembar = maks 10 blok)
     Hanya muncul saat window.print(). Dipindai kamera per lembar.
     ═══════════════════════════════════════════════════════════════ --}}
@php
    $printChunks = array_chunk($soalList instanceof \Illuminate\Support\Collection ? $soalList->all() : (array) $soalList, 10);
    $tipeLabel = strtoupper($paket->tipe ?? 'UMUM');
    if (!in_array($tipeLabel, ['PRE-TEST', 'POST-TEST', 'PRETEST', 'POSTTEST'])) {
        $tipeLabel = $tipeLabel === 'KOMBINASI' ? 'PRE & POST-TEST' : $tipeLabel;
    } else {
        $tipeLabel = str_replace(['PRETEST', 'POSTTEST'], ['PRE-TEST', 'POST-TEST'], $tipeLabel);
    }
@endphp
@foreach($printChunks as $sheetIdx => $chunk)
@php
    $qrPayload = urlencode(json_encode([
        'p' => $paket->id,
        's' => strtolower($paket->tipe ?? 'umum'),
        'l' => $sheetIdx + 1,
        't' => count($printChunks),
    ]));
@endphp
<div class="print-sheet" data-sheet="{{ $sheetIdx + 1 }}">

    {{-- Kop / Header --}}
    <div class="ps-header">
        @if(setting('app.logo'))
        <img class="ps-logo-img" src="{{ setting('app.logo') }}" alt="Logo"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
        <svg class="ps-logo" style="display:none;" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        @else
        <svg class="ps-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        @endif
            <circle cx="50" cy="50" r="47" fill="none" stroke="#000" stroke-width="2.5"/>
            <circle cx="50" cy="50" r="40" fill="none" stroke="#000" stroke-width="1"/>
            <polygon points="50,20 55,35 71,35 59,45 63,61 50,51 37,61 41,45 29,35 45,35" fill="none" stroke="#000" stroke-width="1.5"/>
            <text x="50" y="76" text-anchor="middle" font-size="8" font-weight="bold" font-family="Arial,sans-serif">BNN</text>
            <text x="50" y="85" text-anchor="middle" font-size="4" font-family="Arial,sans-serif">REPUBLIK INDONESIA</text>
        </svg>

        <div class="ps-title-block">
            <div class="ps-title">{{ strtoupper($paket->nama_paket) }}</div>
            <div class="ps-field">
                <span class="ps-label">NAMA</span>
                <span class="ps-colon">:</span>
                <span class="ps-dots"></span>
            </div>
            <div class="ps-field">
                <span class="ps-label">ASAL SEKOLAH</span>
                <span class="ps-colon">:</span>
                <span class="ps-dots"></span>
            </div>
            <div class="ps-hint">Silang (X) dengan jelas hingga menyentuh sisi kotak — contoh benar: &#9746; — salah: &#9744; kecil di sudut</div>
        </div>

        <div class="ps-side">
            <img class="ps-qr" alt="QR Lembar"
                 src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ $qrPayload }}">
            <div class="ps-nomor">
                <div class="ps-nomor-label">NOMOR :</div>
                <div class="ps-nomor-val"></div>
            </div>
        </div>
    </div>

    {{-- Garis & Instruksi --}}
    <hr class="ps-hr">
    <p class="ps-instruksi">Pilih satu jawaban yang paling benar! @if(count($printChunks) > 1)<span class="ps-sheetno">— Lembar {{ $sheetIdx + 1 }} dari {{ count($printChunks) }} —</span>@endif</p>

    {{-- Blok soal (maks 10 per lembar) --}}
    <ol class="ps-soal-list">
        @foreach ($chunk as $item)
        @php $baseNo = $sheetIdx * 10; @endphp
        <li class="ps-soal" data-nomor="{{ $loop->iteration + $baseNo }}">
            <div class="ps-soal-q"><span class="ps-no">{{ $loop->iteration + $baseNo }}.</span> {{ $item->pertanyaan }}</div>
            <div class="ps-opts">
                @foreach (['A', 'B', 'C', 'D'] as $opt)
                <span class="ps-opt"><span class="ps-box" data-opt="{{ $opt }}">{{ $opt }}</span><span class="ps-opt-text">{{ $item->opsi[$opt] ?? '' }}</span></span>
                @endforeach
            </div>
        </li>
        @endforeach
    </ol>

    {{-- Footer Slogan (lembar terakhir saja) --}}
    @if($sheetIdx === count($printChunks) - 1)
    <div class="ps-footer">
        "Masa Depan Cerah dimulai dari Keputusan Hari ini untuk berkata TIDAK pada Narkoba"
    </div>
    @endif
</div>
@endforeach

{{-- Pratinjau lembar di layar (tanpa print) --}}
<script>
(function () {
    const btn = document.getElementById('btnPreviewSheet');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const on = document.body.classList.toggle('show-print-preview');
        btn.querySelector('span').textContent = on ? 'Tutup Pratinjau' : 'Pratinjau Lembar';
        if (on) document.querySelector('.print-sheet')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
})();
</script>
<style>
/* Pratinjau: tampilkan lembar cetak di layar menyerupai kertas A4 */
body.show-print-preview .print-sheet {
    display: block !important;
    background: #fff; color: #000;
    font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.3;
    border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 14mm 12mm; margin: 0 0 16px 0;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
body.show-print-preview .print-sheet .ps-header { display:flex; align-items:flex-start; gap:8px; margin-bottom:4px }
body.show-print-preview .print-sheet .ps-logo { width:56px; height:56px; flex-shrink:0 }
body.show-print-preview .print-sheet .ps-logo-img { width:56px; height:56px; flex-shrink:0; object-fit:contain }
body.show-print-preview .print-sheet .ps-title-block { flex:1 }
body.show-print-preview .print-sheet .ps-title { font-size:11pt; font-weight:bold; text-transform:uppercase; margin-bottom:2px }
body.show-print-preview .print-sheet .ps-field { display:flex; align-items:baseline; line-height:1.7 }
body.show-print-preview .print-sheet .ps-label { font-weight:bold; min-width:96px; font-size:8.5pt }
body.show-print-preview .print-sheet .ps-colon { font-weight:bold; margin:0 4px }
body.show-print-preview .print-sheet .ps-dots { flex:1; border-bottom:1px solid #333 }
body.show-print-preview .print-sheet .ps-hint { font-size:7pt; color:#333; margin-top:2px }
body.show-print-preview .print-sheet .ps-side { display:flex; flex-direction:column; align-items:center; gap:4px }
body.show-print-preview .print-sheet .ps-qr { width:64px; height:64px }
body.show-print-preview .print-sheet .ps-nomor { border:2px solid #000; padding:3px 8px; text-align:center; min-width:70px }
body.show-print-preview .print-sheet .ps-nomor-label { font-size:8pt; font-weight:bold; white-space:nowrap }
body.show-print-preview .print-sheet .ps-nomor-val { height:16px }
body.show-print-preview .print-sheet .ps-hr { border:none; border-top:2px solid #000; margin:4px 0 3px }
body.show-print-preview .print-sheet .ps-instruksi { font-weight:bold; margin:0 0 3px; font-size:9pt }
body.show-print-preview .print-sheet .ps-sheetno { font-weight:normal; font-size:8pt }
body.show-print-preview .print-sheet .ps-soal-list { margin:0; padding:0; list-style:none }
body.show-print-preview .print-sheet .ps-soal { margin:0 0 4px; padding:4px 6px; border:1.2pt solid #000; border-radius:2px }
body.show-print-preview .print-sheet .ps-soal-q { font-weight:600; margin-bottom:3px; font-size:9pt }
body.show-print-preview .print-sheet .ps-no { font-weight:bold; margin-right:2px }
body.show-print-preview .print-sheet .ps-opts { display:grid; grid-template-columns:1fr 1fr; gap:2px 10px }
body.show-print-preview .print-sheet .ps-opt { display:flex; align-items:flex-start; gap:4px; font-size:8.5pt; line-height:1.3 }
body.show-print-preview .print-sheet .ps-box { flex-shrink:0; width:6mm; height:6mm; margin-top:0.5mm; border:1.2pt solid #000; border-radius:1px; display:inline-flex; align-items:center; justify-content:center; font-size:8pt; font-weight:bold }
body.show-print-preview .print-sheet .ps-opt-text { flex:1 }
body.show-print-preview .print-sheet .ps-footer { text-align:center; font-weight:bold; font-size:8.5pt; border-top:1.5px solid #000; padding-top:3px; margin-top:4px }
</style>

{{-- ═══════════════════════════════════════════════════════════════
     STYLES
     ═══════════════════════════════════════════════════════════════ --}}
<style>
/* ─── SCREEN ─────────────────────────────────────────────────────── */
.bs-breadcrumb { display:flex;align-items:center;gap:.5rem;font-size:.8125rem;margin-bottom:1.25rem;color:var(--text-muted) }
.bs-bc-link { display:inline-flex;align-items:center;gap:.35rem;color:var(--text-secondary);text-decoration:none;font-weight:500;transition:color .15s }
.bs-bc-link:hover { color:var(--primary) }
.bs-bc-sep { opacity:.4 }
.bs-bc-current { font-weight:600;color:var(--text-primary);max-width:320px }

.bs-header-card { border-radius:var(--r-xl);background:var(--surface);border:1px solid var(--border);padding:1.5rem;margin-bottom:1.5rem;box-shadow:var(--shadow-sm) }
.bs-icon-box { width:48px;height:48px;border-radius:var(--r-lg);background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(67,97,238,.18) }

.bs-questions-wrap { border-radius:var(--r-xl);background:var(--surface);border:1px solid var(--border);padding:1.5rem;box-shadow:var(--shadow-sm) }

.bs-qcard { background:var(--bg-alt);border:1px solid var(--border);border-radius:var(--r-lg);padding:1.125rem 1.25rem;transition:border-color .15s }
.bs-qcard:hover { border-color:rgba(67,97,238,.25) }
.bs-qnum { width:28px;height:28px;border-radius:var(--r-md);background:var(--primary);color:#fff;font-weight:700;font-size:.8125rem;display:flex;align-items:center;justify-content:center;flex-shrink:0 }
.bs-qtext { font-size:.9375rem;font-weight:600;color:var(--text-primary);line-height:1.5;margin:0;flex:1 }

.bs-opts { display:flex;flex-direction:column;gap:.5rem;padding-left:2.5rem }
@media(max-width:640px){.bs-opts{padding-left:0}}
.bs-opt { display:flex;align-items:center;gap:.75rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);padding:.6rem .875rem;font-size:.8375rem;color:var(--text-secondary);transition:all .15s }
.bs-opt.is-correct { background:rgba(34,197,94,.08);border-color:rgba(34,197,94,.35);color:var(--text-primary);font-weight:500 }
.bs-obadge { width:22px;height:22px;border-radius:var(--r-xs);background:var(--bg-alt);color:var(--text-secondary);font-weight:700;font-size:.72rem;display:flex;align-items:center;justify-content:center;flex-shrink:0 }
.bs-obadge.is-correct-badge { background:#22C55E;color:#fff }
.bs-otext { flex:1;line-height:1.4 }
.bs-keytag { display:inline-flex;align-items:center;gap:.25rem;font-size:.695rem;font-weight:700;color:#15803D;background:#DCFCE7;padding:.2rem .55rem;border-radius:var(--r-full);white-space:nowrap }

/* Print sheet tersembunyi di layar */
.print-sheet { display:none }

/* ─── PRINT ──────────────────────────────────────────────────────── */
@media print {
    @page { size: A4 portrait; margin: 7mm 10mm 6mm 10mm }

    /* Reset & sembunyikan semua elemen web — berbasis struktur, bukan nama class
       (kebal terhadap restyle kartu/breadcrumb di masa depan) */
    body, html { margin:0!important; padding:0!important; background:#fff!important }

    .sidebar, .topbar, header, #sidebarOverlay,
    #sidebarToggle, #themeToggleBtn,
    .modal-overlay, #globalToastPopup, #lokasiModalOverlay {
        display:none !important;
    }

    .main-wrapper { margin:0!important; padding:0!important; max-width:100%!important }
    .main-content { padding:0!important; margin:0!important }

    /* Sembunyikan SEMUA anak konten kecuali lembar cetak */
    .main-content > :not(.print-sheet) {
        display:none !important;
    }

    /* Tampilkan hanya print-sheet */
    .print-sheet {
        display:block !important;
        width:100%;
        background:#fff; color:#000;
        font-family:Arial,sans-serif; font-size:10pt; line-height:1.3;
        page-break-after:always;
    }
    .print-sheet:last-of-type { page-break-after:auto; }

    /* --- Kop --- */
    .ps-header { display:flex; align-items:flex-start; gap:8px; margin-bottom:4px }
    .ps-logo { width:56px; height:56px; flex-shrink:0 }
    .ps-logo-img { width:56px; height:56px; flex-shrink:0; object-fit:contain }
    .ps-title-block { flex:1 }
    .ps-title { font-size:11pt; font-weight:bold; text-transform:uppercase; margin-bottom:2px }
    .ps-tipe { white-space:nowrap }
    .ps-field { display:flex; align-items:baseline; line-height:1.7 }
    .ps-label { font-weight:bold; min-width:96px; font-size:8.5pt }
    .ps-colon { font-weight:bold; margin:0 4px }
    .ps-dots { flex:1; border-bottom:1px solid #333 }
    .ps-hint { font-size:7pt; color:#333; margin-top:2px }
    .ps-side { flex-shrink:0; display:flex; flex-direction:column; align-items:center; gap:4px }
    .ps-qr { width:64px; height:64px }
    .ps-nomor { border:2px solid #000; padding:3px 8px; text-align:center; min-width:70px }
    .ps-nomor-label { font-size:8pt; font-weight:bold; white-space:nowrap }
    .ps-nomor-val { height:16px }

    /* --- Divider & Instruksi --- */
    .ps-hr { border:none; border-top:2px solid #000; margin:4px 0 3px }
    .ps-instruksi { font-weight:bold; margin:0 0 3px; font-size:9pt }
    .ps-sheetno { font-weight:normal; font-size:8pt }

    /* --- Blok soal: bingkai deteksi per blok (jangan ubah kelas ini) --- */
    .ps-soal-list { margin:0; padding:0; list-style:none }
    .ps-soal {
        margin:0 0 4px; padding:4px 6px;
        border:1.2pt solid #000; border-radius:2px;
        page-break-inside:avoid;
    }
    .ps-soal-q { font-weight:600; margin-bottom:3px; font-size:9pt }
    .ps-no { font-weight:bold; margin-right:2px }
    .ps-opts { display:grid; grid-template-columns:1fr 1fr; gap:2px 10px }
    .ps-opt { display:flex; align-items:flex-start; gap:4px; font-size:8.5pt; line-height:1.3 }
    .ps-box {
        flex-shrink:0; width:6mm; height:6mm; margin-top:0.5mm;
        border:1.2pt solid #000; border-radius:1px;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:8pt; font-weight:bold;
    }
    .ps-opt-text { flex:1 }

    /* --- Footer --- */
    .ps-footer { text-align:center; font-weight:bold; font-size:8.5pt; border-top:1.5px solid #000; padding-top:3px; margin-top:4px }
}
</style>

@endsection
