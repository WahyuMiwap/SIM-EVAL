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
            {{-- Session Label --}}
            <div class="flex-shrink-0">
                <span class="badge {{ $quizType === 'pretest' ? 'badge-blue' : 'badge-cyan' }}">
                    {{ $quizType === 'pretest' ? 'Pre-Test' : 'Post-Test' }}
                </span>
            </div>

            {{-- Progress Bar --}}
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-slate-500">Soal <span id="currentSoalNum">1</span> dari <span id="totalSoalNum">{{ count($soal) }}</span></span>
                    <span class="text-xs text-slate-500" id="answeredCount">0 terjawab</span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>

            {{-- Timer --}}
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-slate-600 mb-0.5">Sisa Waktu</p>
                <div class="timer-display" id="timerDisplay">{{ gmdate('i:s', $durasi * 60) }}</div>
            </div>
        </div>
    </div>

    {{-- Quiz Body --}}
    <div class="max-w-2xl mx-auto px-4 py-6">

        {{-- Question Card --}}
        <div class="question-card mb-5 animate-fade-in" id="questionCard">
            {{-- Nomor Soal --}}
            <div class="flex items-center gap-2 mb-4">
                <span class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold text-sm" id="questionNumber">1</span>
                <span class="text-xs text-slate-600 font-medium">SOAL</span>
            </div>

            {{-- Pertanyaan --}}
            <p class="text-slate-100 text-base leading-relaxed mb-6 font-medium" id="questionText">
                {{-- Diisi oleh QuizEngine.js --}}
            </p>

            {{-- Pilihan Jawaban --}}
            <div id="optionsContainer">
                @foreach(['A','B','C','D'] as $opt)
                <button type="button" class="option-btn" data-option="{{ $opt }}" id="option{{ $opt }}">
                    <span class="option-letter">{{ $opt }}</span>
                    <span class="option-text">...</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between gap-3">
            <button type="button" class="btn btn-secondary" id="btnPrev" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </button>

            {{-- Soal Grid --}}
            <div class="flex flex-wrap gap-1.5 justify-center flex-1" id="soalNavGrid">
                @foreach($soal as $i => $s)
                <button type="button"
                    class="w-8 h-8 rounded-lg text-xs font-bold transition-all duration-150 bg-white/5 border border-white/8 text-slate-500 hover:border-blue-500/50 hover:text-slate-300"
                    data-nav="{{ $i }}"
                    id="navBtn{{ $i }}">{{ $i + 1 }}</button>
                @endforeach
            </div>

            <button type="button" class="btn btn-primary" id="btnNext">
                Berikutnya
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Submit Button (muncul saat soal terakhir) --}}
        <div class="mt-4 hidden" id="submitSection">
            <button type="button" class="btn btn-success w-full btn-lg" id="btnSubmitQuiz">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kirim Jawaban
            </button>
            <p class="text-xs text-center text-slate-500 mt-2">Pastikan semua soal sudah terjawab sebelum mengirim.</p>
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
<div class="modal-overlay" id="warningTimerOverlay">
    <div class="modal-box text-center" style="max-width: 360px;">
        <div class="w-16 h-16 rounded-full bg-amber-500/15 border border-amber-500/30 flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-white font-display">Waktu Hampir Habis!</h3>
        <p class="text-sm text-slate-400 mt-2">Sisa waktu <span class="text-amber-400 font-bold" id="warningCountdown">15</span> detik. Jawaban akan otomatis terkirim!</p>
        <div class="mt-5">
            <div class="progress-bar-track">
                <div class="progress-bar-fill" id="warningProgressBar" style="width: 100%; background: linear-gradient(90deg, #f59e0b, #ef4444);"></div>
            </div>
        </div>
        <button type="button" class="btn btn-primary w-full mt-4" id="btnSubmitWarning">
            Kirim Sekarang
        </button>
    </div>
</div>

{{-- Auto-submit Done Screen --}}
<div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/90 backdrop-blur-sm hidden" id="doneScreen">
    <div class="text-center px-6">
        <div class="w-20 h-20 rounded-full bg-green-500/20 border border-green-500/40 flex items-center justify-center mx-auto mb-5 animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white font-display">Jawaban Terkirim!</h2>
        <p class="text-slate-400 text-sm mt-2" id="doneMessage">
            @if($quizType === 'pretest')
                Pre-Test selesai. Silakan tunggu instruktur untuk sesi berikutnya.
            @else
                Post-Test selesai. Terima kasih atas partisipasi Anda!
            @endif
        </p>
        <div class="glass-card px-5 py-3 mt-5 inline-block">
            <p class="text-xs text-slate-500">Skor sementara</p>
            <p class="text-3xl font-bold text-white font-display mt-1" id="scoreDisplay">—</p>
        </div>
        @if($quizType === 'pretest')
            <p class="text-xs text-slate-600 mt-4">Halaman akan otomatis berpindah ke Waiting Room Post-Test...</p>
        @endif
    </div>
</div>

@endsection
