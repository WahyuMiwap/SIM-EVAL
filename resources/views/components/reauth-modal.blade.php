{{--
    Re-Authentication Modal (FR-43)
    Digunakan untuk konfirmasi aksi hapus pada Kegiatan, Lokasi, Bank Soal.
    JS Controller: resources/js/modules/ReauthModal.js
--}}
<div class="modal-overlay" id="reauthModalOverlay" role="dialog" aria-modal="true" aria-labelledby="reauthModalTitle">
    <div class="modal-box" style="max-width: 440px;" id="reauthModalBox">

        {{-- Icon Danger --}}
        <div class="reauth-danger-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        {{-- Header --}}
        <div class="text-center mb-5">
            <h3 class="text-lg font-bold font-display" style="color:var(--text-primary);" id="reauthModalTitle">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-slate-400 mt-1.5" id="reauthModalDesc">
                Masukkan password akun Anda untuk mengonfirmasi penghapusan data ini. Tindakan ini tidak dapat dibatalkan.
            </p>
            {{-- Item yang akan dihapus --}}
            <div class="mt-3 px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/20 text-sm text-red-300 font-medium" id="reauthItemLabel">
                —
            </div>
        </div>

        {{-- Password Input --}}
        <div class="form-group">
            <label class="form-label" for="reauthPassword">Password Akun Anda</label>
            <div class="relative">
                <input
                    type="password"
                    id="reauthPassword"
                    class="form-input pr-12"
                    placeholder="Masukkan password Anda"
                    autocomplete="current-password"
                />
                <button type="button" id="reauthPasswordToggle" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" id="eyeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            {{-- Error message --}}
            <p class="text-xs text-red-400 mt-1.5 hidden" id="reauthError">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Password salah. Coba lagi.
            </p>
        </div>

        {{-- Hidden Fields --}}
        <input type="hidden" id="reauthActionUrl" value="">
        <input type="hidden" id="reauthItemId" value="">
        <input type="hidden" id="reauthMethod" value="DELETE">

        {{-- Actions --}}
        <div class="flex gap-3 mt-6">
            <button type="button" id="reauthCancelBtn" class="btn btn-secondary flex-1">
                Batal
            </button>
            <button type="button" id="reauthConfirmBtn" class="btn btn-danger flex-1" id="reauthConfirmBtn">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" id="reauthSpinnerIcon" style="display:none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Hapus Data
            </button>
        </div>
    </div>
</div>
