@extends('layouts.participant')

@section('title', $quizType === 'pretest' ? 'Pre-Test' : 'Post-Test')

@section('content')
<div class="quiz-wrapper"
    id="quizWrapper"
    data-session="{{ $session->id }}"
    data-type="{{ $quizType }}"
    data-total="{{ count($soal) }}"
    data-durasi="{{ $durasi }}"
    data-submit-url="{{ route('participant.submit') }}">

    {{-- Quiz Header (Sticky) --}}
    <div class="quiz-header">
        <div class="flex items-center gap-4">
            {{-- Logo di Pojok Kiri Atas --}}
            <div class="flex-shrink-0 flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs" style="background: var(--primary);" title="SIM-EVAL P2M BNN Surabaya">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                        <path d="M3 20h18"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-sm leading-none hidden sm:inline tracking-tight" style="color: var(--text-primary);">SIM-EVAL</span>
            </div>

            {{-- Progress Bar & Status Tag --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1.5 gap-2">
                    <div class="flex items-center gap-2 truncate">
                        <span class="text-xs text-slate-500">Soal <span id="currentSoalNum" class="font-bold text-slate-700 dark:text-slate-200">1</span> dari <span id="totalSoalNum">{{ count($soal) }}</span></span>
                        <span class="text-slate-300 dark:text-slate-600 hidden sm:inline">&bull;</span>
                        <span class="font-display font-bold text-xs sm:text-sm tracking-tight" style="color: var(--text-primary);">
                            {{ $quizType === 'pretest' ? 'Pre-Test' : 'Post-Test' }}
                        </span>
                    </div>
                    <span class="text-xs text-slate-400 flex-shrink-0" id="answeredCount">0 terjawab</span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>

            {{-- Timer --}}
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-slate-500 mb-0.5">Sisa Waktu</p>
                <div class="timer-display" id="timerDisplay">{{ gmdate('i:s', $durasi * 60) }}</div>
            </div>
        </div>
    </div>

    {{-- Quiz Body with Left Sidebar for Question Numbers --}}
    <div class="max-w-4xl mx-auto px-2.5 sm:px-4 py-3 sm:py-5">
        <div class="flex items-start gap-2.5 sm:gap-4">

            {{-- Left Sidebar: Daftar Soal (Scrollable Rail) --}}
            <aside class="w-14 sm:w-16 shrink-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-1 sm:p-1.5 shadow-xs sticky top-[72px] flex flex-col z-20 overflow-hidden" style="max-height: calc(100vh - 90px);">
                <div class="py-1 px-0.5 mb-1.5 border-b border-slate-200/80 dark:border-slate-800 text-center flex flex-col items-center">
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Soal</span>
                    <span class="text-[11px] sm:text-xs font-black text-primary leading-tight"><span id="sidebarActiveNum">1</span>/<span class="text-slate-400 font-normal">{{ count($soal) }}</span></span>
                </div>

                {{-- Scrollable Buttons List (Hanya sidebar yang scroll, scrollbar slider disembunyikan total) --}}
                <div class="flex-1 overflow-y-auto overflow-x-hidden space-y-1.5 px-0.5 py-0.5 flex flex-col items-center no-scrollbar" id="soalNavGrid" style="scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach($soal as $i => $s)
                    <button type="button"
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center shrink-0 {{ $i === 0 ? 'bg-blue-600 border border-blue-500 text-white shadow-sm ring-2 ring-blue-400/30' : 'bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-primary/50 hover:text-primary' }}"
                        data-nav="{{ $i }}"
                        id="navBtn{{ $i }}"
                        title="Nomor Soal {{ $i + 1 }}">{{ $i + 1 }}</button>
                    @endforeach
                </div>
            </aside>

            {{-- Main Content: Question Card + Action Buttons --}}
            <main class="flex-1 min-w-0 space-y-3.5">
                {{-- Question Card --}}
                <div class="question-card !p-4 sm:!p-7 animate-fade-in" id="questionCard">
                    {{-- Nomor Soal --}}
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center text-primary font-bold text-sm" id="questionNumber">1</span>
                        <span class="text-xs text-slate-400 font-semibold tracking-wider">SOAL</span>
                    </div>

                    {{-- Pertanyaan --}}
                    <p class="text-slate-800 dark:text-slate-100 text-sm sm:text-base leading-relaxed mb-5 font-semibold" id="questionText">
                        {{-- Diisi oleh QuizEngine.js --}}
                    </p>

                    {{-- Pilihan Jawaban --}}
                    <div id="optionsContainer">
                        @foreach(['A','B','C','D'] as $opt)
                        <button type="button" class="option-btn !py-2.5 sm:!py-3.5 !px-3 sm:!px-4.5" data-option="{{ $opt }}" id="option{{ $opt }}">
                            <span class="option-letter">{{ $opt }}</span>
                            <span class="option-text">...</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons: Sebelumnya & Berikutnya --}}
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <button type="button" class="btn btn-secondary flex-1 py-2.5 sm:py-3 justify-center text-sm font-semibold rounded-xl" id="btnPrev" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Sebelumnya
                    </button>

                    <button type="button" class="btn btn-primary flex-1 py-2.5 sm:py-3 justify-center text-sm font-semibold rounded-xl" id="btnNext">
                        Berikutnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                {{-- Submit Button (muncul saat soal terakhir) --}}
                <div class="hidden" id="submitSection">
                    <button type="button" class="btn btn-success w-full py-3 text-base font-bold rounded-xl shadow-md shadow-emerald-500/20" id="btnSubmitQuiz">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Kirim Jawaban
                    </button>
                    <p class="text-xs text-center text-slate-500 mt-2">Pastikan semua soal sudah terjawab sebelum mengirim.</p>
                </div>
            </main>
        </div>
    </div>

    {{-- Hidden: Soal Data (untuk QuizEngine.js, diacak per peserta FR-24c) --}}
    <script id="quizData" type="application/json">
        {!! json_encode([
            'sessionId'  => $session->id,
            'quizType'   => $quizType,
            'soal'       => $soal,
            'durasi'     => $durasi,
            'submitUrl'  => route('participant.submit'),
            'csrfToken'  => csrf_token(),
        ]) !!}
    </script>
</div>

{{-- Warning Modal (15 Detik Terakhir, FR-24b) --}}
<div class="fixed inset-0 z-[220] hidden items-center justify-center bg-slate-900/45 backdrop-blur-sm p-4" id="warningTimerOverlay">
    <div class="w-full max-w-sm rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 text-center shadow-2xl">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-900/50 flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 font-display">Waktu Hampir Habis!</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Sisa waktu <span class="text-amber-500 font-bold" id="warningCountdown">15</span> detik. Jawaban akan otomatis terkirim!</p>
        <div class="mt-4">
            <div class="progress-bar-track">
                <div class="progress-bar-fill" id="warningProgressBar" style="width: 100%; background: linear-gradient(90deg, #f59e0b, #ef4444);"></div>
            </div>
        </div>
        <button type="button" class="btn btn-primary w-full mt-5 py-2.5 rounded-xl font-semibold" id="btnSubmitWarning">
            Kirim Sekarang
        </button>
    </div>
</div>

{{-- Modal Review Akhir (konfirmasi kirim + jeda paksa 3 detik) --}}
<div class="fixed inset-0 z-[210] hidden items-center justify-center bg-slate-900/45 backdrop-blur-sm p-4" id="reviewOverlay">
    <div class="w-full max-w-sm rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 text-center shadow-2xl animate-scale-in">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900/50 flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 font-display">Jawaban Sudah Sesuai?</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5" id="reviewSummary">Terjawab 0 dari 0 soal.</p>
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 hidden font-medium text-center leading-relaxed" id="reviewBlankNote"></p>
        <div class="flex gap-2.5 mt-5">
            <button type="button" class="btn btn-secondary flex-1 py-2.5 rounded-xl font-semibold justify-center" id="btnReviewBack">
                Kembali ke Soal
            </button>
            <button type="button" class="btn btn-primary flex-1 py-2.5 rounded-xl font-semibold justify-center opacity-50 cursor-not-allowed" id="btnReviewSend" disabled>
                Kirim (3)
            </button>
        </div>
    </div>
</div>

{{-- Auto-submit Done Screen --}}
<div class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/45 backdrop-blur-sm hidden p-4" id="doneScreen">
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 max-w-md w-full text-center shadow-2xl">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/80 dark:border-emerald-900/50 flex items-center justify-center mx-auto mb-4 animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white font-display">Jawaban Terkirim!</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2" id="doneMessage">
            @if($quizType === 'pretest')
                Pre-Test selesai. Silakan tunggu instruktur untuk sesi berikutnya.
            @else
                Post-Test selesai. Terima kasih atas partisipasi Anda!
            @endif
        </p>
        <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 px-6 py-3.5 mt-5 rounded-2xl inline-block">
            <p class="text-xs text-slate-400">Skor sementara</p>
            <p class="text-3xl font-bold text-primary font-display mt-1" id="scoreDisplay">—</p>
        </div>
        @if($quizType === 'pretest')
            <p class="text-xs text-slate-400 mt-4">Halaman akan otomatis berpindah ke Waiting Room Post-Test...</p>
        @endif
    </div>
</div>

<style>
    #soalNavGrid::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    #soalNavGrid {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
</style>

@endsection
