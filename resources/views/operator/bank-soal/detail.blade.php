@extends('layouts.app')

@section('title', 'Detail Paket Soal — ' . ($paket->nama_paket ?? 'Bank Soal'))
@section('page-title', 'Detail Paket Soal')
@section('page-subtitle', 'Kelola butir pertanyaan dan kunci jawaban instrumen tes P2M')

@section('content')

{{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
<div class="bs-breadcrumb no-print">
    <a href="{{ route('operator.bank-soal.index') }}" class="bs-bc-link">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Bank Soal</span>
    </a>
    <span class="bs-bc-sep">/</span>
    <span class="bs-bc-current truncate">{{ $paket->nama_paket }}</span>
</div>

{{-- ── Header Card ────────────────────────────────────────────── --}}
<div class="glass animate-fade-in bs-detail-header-card" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Title & Info --}}
        <div class="flex items-start gap-3.5">
            <div class="bs-icon-box-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl" style="color: var(--text-primary); margin: 0 0 0.4rem; letter-spacing: -0.02em;">
                    {{ $paket->nama_paket }}
                </h2>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="badge badge-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ count($soalList) }} Butir Soal
                    </span>
                    <span class="badge badge-gray">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Durasi: {{ $paket->durasi ?? 30 }} Menit
                    </span>
                    <span class="badge badge-green">
                        Seksi P2M &middot; BNN Kota Surabaya
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2.5 flex-wrap no-print">
            {{-- Cetak Soal Kertas --}}
            <button type="button" class="btn btn-secondary" onclick="window.print()" title="Cetak Lembar Soal / Simpan ke PDF">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Soal Kertas</span>
            </button>

            {{-- Edit Paket Soal --}}
            <a href="{{ route('operator.bank-soal.edit', $paket->id) }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Paket Soal</span>
            </a>
        </div>
    </div>
</div>

{{-- ── Print Header (Only shown during window.print) ───────────── --}}
<div class="print-only bs-print-header">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px;">
        <h2 style="font-size: 16pt; font-weight: bold; margin: 0; text-transform: uppercase;">Badan Narkotika Nasional Kota Surabaya</h2>
        <h3 style="font-size: 13pt; margin: 4px 0 0; font-weight: 600;">Seksi Pencegahan dan Pemberdayaan Masyarakat (P2M)</h3>
        <p style="font-size: 11pt; margin: 4px 0 0;">Lembar Instrumen Evaluasi Pemahaman Materi Sosialisasi P4GN</p>
    </div>
    <table style="width: 100%; margin-bottom: 20px; font-size: 10pt;">
        <tr>
            <td style="width: 15%; font-weight: bold;">Paket Soal</td>
            <td style="width: 45%;">: {{ $paket->nama_paket }}</td>
            <td style="width: 15%; font-weight: bold;">Hari / Tgl</td>
            <td style="width: 25%;">: ........................................</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Waktu</td>
            <td>: {{ $paket->durasi ?? 30 }} Menit</td>
            <td style="font-weight: bold;">Nama Peserta</td>
            <td>: ........................................</td>
        </tr>
    </table>
</div>

{{-- ── Questions List Container ────────────────────────────────── --}}
<div class="glass bs-questions-container" style="border-radius: var(--r-xl); background: var(--surface); border: 1px solid var(--border); padding: 1.5rem; box-shadow: var(--shadow-sm);">
    
    <div class="flex items-center justify-between pb-3 mb-4" style="border-bottom: 1px solid var(--border);">
        <div>
            <h3 class="font-display font-bold text-base" style="color: var(--text-primary); margin: 0 0 0.15rem;">
                Daftar Butir Pertanyaan
            </h3>
            <p class="text-xs" style="color: var(--text-muted); margin: 0;">
                Kunci jawaban yang benar ditandai dengan warna hijau khusus untuk panduan operator.
            </p>
        </div>
        <span class="text-xs font-semibold" style="color: var(--text-secondary);">
            Total {{ count($soalList) }} Soal
        </span>
    </div>

    <div class="space-y-4">
        @forelse ($soalList as $idx => $item)
        <div class="bs-question-card">
            {{-- Question Header --}}
            <div class="flex items-start gap-3 mb-3">
                <span class="bs-q-num">
                    {{ $idx + 1 }}
                </span>
                <div class="flex-1">
                    <p class="bs-q-text">
                        {{ $item->pertanyaan }}
                    </p>
                </div>
            </div>

            {{-- Options List --}}
            <div class="bs-options-grid">
                @foreach (['A', 'B', 'C', 'D'] as $optKey)
                    @php 
                        $isKey = ($item->kunci === $optKey);
                        $optText = $item->opsi[$optKey] ?? '—';
                    @endphp
                    <div class="bs-option-item {{ $isKey ? 'is-correct' : '' }}">
                        <span class="bs-option-badge {{ $isKey ? 'is-correct-badge' : '' }}">
                            {{ $optKey }}
                        </span>
                        <span class="bs-option-text">{{ $optText }}</span>
                        @if($isKey)
                        <span class="bs-key-tag no-print">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Kunci Jawaban
                        </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-slate-500 text-sm">
            Belum ada butir soal dalam paket ini. Klik tombol <strong>Edit Paket Soal</strong> untuk menambahkan pertanyaan.
        </div>
        @endforelse
    </div>

    {{-- Bottom Navigation --}}
    <div class="flex items-center justify-between pt-5 mt-6 no-print" style="border-top: 1px solid var(--border);">
        <a href="{{ route('operator.bank-soal.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Bank Soal</span>
        </a>

        <a href="{{ route('operator.bank-soal.edit', $paket->id) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span>Edit Paket Ini</span>
        </a>
    </div>
</div>

{{-- ── Styles ─────────────────────────────────────────────────── --}}
<style>
/* Breadcrumb */
.bs-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    margin-bottom: 1.25rem;
    color: var(--text-muted);
}
.bs-bc-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s ease;
}
.bs-bc-link:hover {
    color: var(--primary);
}
.bs-bc-sep {
    opacity: 0.4;
}
.bs-bc-current {
    font-weight: 600;
    color: var(--text-primary);
    max-width: 320px;
}

/* Icon box */
.bs-icon-box-lg {
    width: 48px;
    height: 48px;
    border-radius: var(--r-lg);
    background: var(--primary-light);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(67,97,238,0.18);
}

/* Question Card */
.bs-question-card {
    background: var(--bg-alt);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    padding: 1.125rem 1.25rem;
    transition: all 0.15s ease;
}
.bs-question-card:hover {
    border-color: rgba(67,97,238,0.25);
}
.bs-q-num {
    width: 28px;
    height: 28px;
    border-radius: var(--r-md);
    background: var(--primary);
    color: #fff;
    font-weight: 700;
    font-size: 0.8125rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bs-q-text {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.5;
    margin: 0;
}

/* Options */
.bs-options-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding-left: 2.5rem;
}
@media (max-width: 640px) {
    .bs-options-grid { padding-left: 0; }
}
.bs-option-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 0.6rem 0.875rem;
    font-size: 0.8375rem;
    color: var(--text-secondary);
    transition: all 0.15s ease;
}
.bs-option-badge {
    width: 22px;
    height: 22px;
    border-radius: var(--r-xs);
    background: var(--bg-alt);
    color: var(--text-secondary);
    font-weight: 700;
    font-size: 0.72rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.bs-option-text {
    flex: 1;
    line-height: 1.4;
}

/* Correct Answer Highlighting */
.bs-option-item.is-correct {
    background: rgba(34, 197, 94, 0.08);
    border-color: rgba(34, 197, 94, 0.35);
    color: var(--text-primary);
    font-weight: 500;
}
.bs-option-badge.is-correct-badge {
    background: #22C55E;
    color: #fff;
}
.bs-key-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.695rem;
    font-weight: 700;
    color: #15803D;
    background: #DCFCE7;
    padding: 0.2rem 0.55rem;
    border-radius: var(--r-full);
    white-space: nowrap;
}

/* Print Styles */
.print-only {
    display: none;
}
@media print {
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .no-print,
    .topbar,
    .sidebar,
    .sidebar-footer,
    #mainWrapper > header,
    #sidebarToggle,
    #themeToggleBtn {
        display: none !important;
    }
    .main-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
    .main-content {
        padding: 0 !important;
    }
    .print-only {
        display: block !important;
    }
    .bs-detail-header-card {
        display: none !important;
    }
    .bs-questions-container {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        background: transparent !important;
    }
    .bs-question-card {
        border: 1px solid #ddd !important;
        background: #fff !important;
        break-inside: avoid;
        margin-bottom: 12px !important;
    }
    .bs-option-item {
        border: 1px solid #eee !important;
        background: #fff !important;
        color: #000 !important;
    }
    .bs-option-item.is-correct {
        background: #fff !important;
        border-color: #000 !important;
        font-weight: bold;
    }
    .bs-option-badge.is-correct-badge {
        background: #000 !important;
        color: #fff !important;
    }
}
</style>

@endsection
