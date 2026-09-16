@extends('layouts.app')

@section('title', 'Detail Paket Soal — ' . ($paket->nama_paket ?? 'Bank Soal'))
@section('page-title', 'Detail Paket Soal')
@section('page-subtitle', 'Kelola butir pertanyaan dan kunci jawaban instrumen tes P2M')

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div class="bs-breadcrumb">
    <a href="{{ route('operator.bank-soal.index') }}" class="bs-bc-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Bank Soal</span>
    </a>
    <span class="bs-bc-sep">/</span>
    <span class="bs-bc-current">{{ $paket->nama_paket }}</span>
</div>

{{-- ── Header Card ────────────────────────────────────────────── --}}
<div class="glass bs-header-card">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="bs-icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl" style="color:var(--text-primary);margin:0 0 .4rem;letter-spacing:-.02em">{{ $paket->nama_paket }}</h2>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="badge badge-blue">{{ count($soalList) }} Butir Soal</span>
                    <span class="badge badge-gray">Durasi: {{ $paket->durasi ?? 30 }} Menit</span>
                    <span class="badge badge-green">Seksi P2M &middot; BNN Kota Surabaya</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" class="btn btn-secondary" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Soal Kertas</span>
            </button>
            <a href="{{ route('operator.bank-soal.edit', $paket->id) }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Paket Soal</span>
            </a>
        </div>
    </div>
</div>

{{-- ── Questions Container ─────────────────────────────────────── --}}
<div class="glass bs-questions-wrap">
    <div class="flex items-center justify-between pb-3 mb-4" style="border-bottom:1px solid var(--border)">
        <div>
            <h3 class="font-display font-bold text-base" style="color:var(--text-primary);margin:0 0 .15rem">Daftar Butir Pertanyaan</h3>
            <p class="text-xs" style="color:var(--text-muted);margin:0">Kunci jawaban yang benar ditandai warna hijau untuk panduan operator.</p>
        </div>
        <span class="text-xs font-semibold" style="color:var(--text-secondary)">Total {{ count($soalList) }} Soal</span>
    </div>

    <div class="space-y-4">
        @forelse ($soalList as $idx => $item)
        <div class="bs-qcard">
            <div class="flex items-start gap-3 mb-3">
                <span class="bs-qnum">{{ $idx + 1 }}</span>
                <p class="bs-qtext">{{ $item->pertanyaan }}</p>
            </div>
            <div class="bs-opts">
                @foreach (['A','B','C','D'] as $k)
                @php $isKey = ($item->kunci === $k); $txt = $item->opsi[$k] ?? '—'; @endphp
                <div class="bs-opt {{ $isKey ? 'is-correct' : '' }}">
                    <span class="bs-obadge {{ $isKey ? 'is-correct-badge' : '' }}">{{ $k }}</span>
                    <span class="bs-otext">{{ $txt }}</span>
                    @if($isKey)
                    <span class="bs-keytag">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Kunci Jawaban
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-center py-12 text-sm" style="color:var(--text-muted)">Belum ada butir soal. Klik <strong>Edit Paket Soal</strong> untuk menambahkan.</p>
        @endforelse
    </div>

    <div class="flex items-center justify-start pt-5 mt-6" style="border-top:1px solid var(--border)">
        <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PRINT SHEET — hanya muncul saat window.print()
     ═══════════════════════════════════════════════════════════════ --}}
<div class="print-sheet" id="printSheet">

    {{-- Kop / Header --}}
    <div class="ps-header">
        {{-- Logo BNN SVG --}}
        <svg class="ps-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="47" fill="none" stroke="#000" stroke-width="2.5"/>
            <circle cx="50" cy="50" r="40" fill="none" stroke="#000" stroke-width="1"/>
            <polygon points="50,20 55,35 71,35 59,45 63,61 50,51 37,61 41,45 29,35 45,35" fill="none" stroke="#000" stroke-width="1.5"/>
            <text x="50" y="76" text-anchor="middle" font-size="8" font-weight="bold" font-family="Arial,sans-serif">BNN</text>
            <text x="50" y="85" text-anchor="middle" font-size="4" font-family="Arial,sans-serif">REPUBLIK INDONESIA</text>
        </svg>

        {{-- Judul & Isian Peserta --}}
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
        </div>

        {{-- Kotak Nomor --}}
        <div class="ps-nomor">
            <div class="ps-nomor-label">NOMOR :</div>
            <div class="ps-nomor-val"></div>
        </div>
    </div>

    {{-- Garis & Instruksi --}}
    <hr class="ps-hr">
    <p class="ps-instruksi">Pilih satu jawaban yang paling benar!</p>

    {{-- Daftar Soal --}}
    <ol class="ps-soal-list">
        @foreach ($soalList as $idx => $item)
        @php
            $A = $item->opsi['A'] ?? '';
            $B = $item->opsi['B'] ?? '';
            $C = $item->opsi['C'] ?? '';
            $D = $item->opsi['D'] ?? '';
            $maxLen = max(mb_strlen($A), mb_strlen($B), mb_strlen($C), mb_strlen($D));
        @endphp
        <li class="ps-soal">
            <div class="ps-soal-q">{{ $item->pertanyaan }}</div>
            @if($maxLen <= 48)
            <div class="ps-opts-grid2">
                <span>A. {{ $A }}</span>
                <span>B. {{ $B }}</span>
                <span>C. {{ $C }}</span>
                <span>D. {{ $D }}</span>
            </div>
            @else
            <div class="ps-opts-grid1">
                <div>A. {{ $A }}</div>
                <div>B. {{ $B }}</div>
                <div>C. {{ $C }}</div>
                <div>D. {{ $D }}</div>
            </div>
            @endif
        </li>
        @endforeach
    </ol>

    {{-- Footer Slogan --}}
    <div class="ps-footer">
        "Masa Depan Cerah dimulai dari Keputusan Hari ini untuk berkata TIDAK pada Narkoba"
    </div>
</div>

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
    @page { size: A4 portrait; margin: 8mm 12mm 7mm 12mm }

    /* Reset & sembunyikan semua elemen web */
    body, html { margin:0!important; padding:0!important; background:#fff!important }

    .sidebar, .topbar, header, #sidebarOverlay,
    #sidebarToggle, #themeToggleBtn,
    .bs-breadcrumb, .bs-header-card, .bs-questions-wrap {
        display:none !important;
    }

    .main-wrapper { margin:0!important; padding:0!important; max-width:100%!important }
    .main-content { padding:0!important; margin:0!important }

    /* Tampilkan hanya print-sheet */
    #printSheet {
        display:block !important;
        width:100%;
        background:#fff; color:#000;
        font-family:Arial,sans-serif; font-size:9.5pt; line-height:1.25;
    }

    /* --- Kop --- */
    .ps-header { display:flex; align-items:flex-start; gap:8px; margin-bottom:4px }
    .ps-logo { width:60px; height:60px; flex-shrink:0 }
    .ps-title-block { flex:1 }
    .ps-title { font-size:11pt; font-weight:bold; text-transform:uppercase; margin-bottom:3px }
    .ps-field { display:flex; align-items:baseline; line-height:1.8 }
    .ps-label { font-weight:bold; min-width:100px; font-size:9pt }
    .ps-colon { font-weight:bold; margin:0 4px }
    .ps-dots { flex:1; border-bottom:1px solid #333 }
    .ps-nomor { flex-shrink:0; border:2px solid #000; padding:4px 10px; text-align:center; min-width:75px }
    .ps-nomor-label { font-size:8.5pt; font-weight:bold; white-space:nowrap }
    .ps-nomor-val { height:20px }

    /* --- Divider & Instruksi --- */
    .ps-hr { border:none; border-top:2px solid #000; margin:5px 0 3px }
    .ps-instruksi { font-weight:bold; margin:0 0 4px; font-size:9.5pt }

    /* --- Soal --- */
    .ps-soal-list { margin:0; padding:0; list-style:none }
    .ps-soal { margin-bottom:4px; page-break-inside:avoid; counter-increment:soal }
    .ps-soal-q { font-weight:600; margin-bottom:1px }
    .ps-soal::before { content: counter(soal) ". "; font-weight:bold }

    /* Opsi 2 kolom (jawaban pendek) */
    .ps-opts-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:0 8px; margin-left:14px }

    /* Opsi 1 kolom (jawaban panjang) */
    .ps-opts-grid1 { margin-left:14px }
    .ps-opts-grid1 div { margin-bottom:1px }

    /* --- Footer --- */
    .ps-footer { text-align:center; font-weight:bold; font-size:9pt; border-top:1.5px solid #000; padding-top:4px; margin-top:5px }
}
</style>

@endsection
