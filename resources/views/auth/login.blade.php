<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ setting('app.nama') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --primary: {{ setting('app.warna_primer') }}; }
        .login-card {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.07), 0 0 0 1px rgba(255, 255, 255, 0.7) inset;
        }
        .dark .login-card {
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        }
        .login-input {
            background: rgba(255, 255, 255, 0.7) !important;
            border-color: rgba(203, 213, 225, 0.8) !important;
        }
        .login-input:focus {
            background: rgba(255, 255, 255, 0.95) !important;
        }
        .dark .login-input {
            background: rgba(30, 41, 59, 0.7) !important;
            border-color: rgba(51, 65, 85, 0.8) !important;
        }
        .dark .login-input:focus {
            background: rgba(30, 41, 59, 0.95) !important;
        }
    </style>
</head>
<body>
@php
    $bgLogin = setting('app.bg_login');
@endphp
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 relative" style="{{ $bgLogin ? "background-image: url('" . asset($bgLogin) . "'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed;" : 'background: var(--bg, #f1f5f9);' }}">
    @if($bgLogin)
        <div class="fixed inset-0 pointer-events-none" style="background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"></div>
    @else
        {{-- Ambient decorative glow agar efek glassmorphism terlihat jelas --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-400/20 blur-3xl"></div>
            <div class="absolute top-1/2 -right-40 w-96 h-96 rounded-full bg-sky-300/20 blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 w-96 h-96 rounded-full bg-indigo-400/15 blur-3xl"></div>
        </div>
    @endif

    <div class="w-full relative z-10" style="max-width: 420px;">
        <div class="login-card rounded-2xl overflow-hidden shadow-lg">
            <div class="p-6 sm:p-8 md:p-9">
                {{-- Brand --}}
                <div class="flex items-center gap-4 sm:gap-4.5 mb-7">
                    @if(setting('app.logo'))
                        <img src="{{ setting('app.logo') }}" alt="Logo" class="w-11 h-11 rounded-xl object-cover border border-slate-200/70 flex-shrink-0">
                    @else
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><path d="M3 20h18"/>
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex flex-col justify-center">
                        <p class="font-display font-bold text-base tracking-wide leading-snug truncate" style="color: var(--text-primary);">{{ setting('app.nama') }}</p>
                        <p class="text-xs leading-normal truncate mt-1.5" style="color: var(--text-muted);">{{ setting('app.subnama') }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h1 class="font-display font-bold text-xl" style="color: var(--text-primary);">Masuk</h1>
                    <p class="text-xs mt-1.5" style="color: var(--text-muted);">Gunakan email dan password akun kedinasan Anda</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold block mb-1.5" style="color: var(--text-secondary);" for="f_email">Email Kedinasan</label>
                        <input type="email" name="email" id="f_email" required maxlength="255" autofocus
                               value="{{ old('email') }}" placeholder="nama@bnnsurabaya.go.id" autocomplete="username"
                               class="form-input login-input w-full rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold block mb-1.5" style="color: var(--text-secondary);" for="f_password">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="f_password" required autocomplete="current-password" placeholder="••••••••"
                                   class="form-input login-input w-full rounded-xl text-sm" style="padding-right: 2.75rem;">
                            <button type="button" id="btnTogglePassword" title="Tampilkan password" aria-label="Tampilkan password"
                                     class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition-colors">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden">
                                    <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('email')
                            <p class="text-[11px] text-rose-500 mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-1">
                        <label class="inline-flex items-center gap-2 text-xs cursor-pointer" style="color: var(--text-secondary);">
                            <input type="checkbox" name="remember" value="1" class="accent-blue-600"> Ingat saya di perangkat ini
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-full rounded-xl py-3 text-sm font-semibold shadow-xs">
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-[11px] mt-6" style="color: {{ $bgLogin ? 'rgba(255,255,255,0.85)' : 'var(--text-muted)' }}; text-shadow: {{ $bgLogin ? '0 1px 3px rgba(0,0,0,0.8)' : 'none' }};">Seksi P2M BNN Kota Surabaya &middot; Akses terotentikasi dan tercatat audit</p>
    </div>
</div>

<script>
(function () {
    var input = document.getElementById('f_password');
    var btn = document.getElementById('btnTogglePassword');
    var open = document.getElementById('eyeOpen');
    var closed = document.getElementById('eyeClosed');
    if (!input || !btn) return;
    btn.addEventListener('click', function () {
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.title = show ? 'Sembunyikan password' : 'Tampilkan password';
        if (open) open.classList.toggle('hidden', show);
        if (closed) closed.classList.toggle('hidden', !show);
    });
})();
</script>
</body>
</html>
