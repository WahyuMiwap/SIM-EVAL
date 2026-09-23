{{--
    Blok Paket Soal Pre/Post-Test — dipakai bersama create & edit.
    Segmented pill: "Satu paket" (default, N-Gain valid) vs "Bedakan".

    Variabel yang diharapkan:
      $paketList    daftar paket [{id, nama_paket, soal_count, ...}]
      $paketSama    bool mode gabungan
      $paketUtamaId id terpilih mode gabungan
      $pretestId    id pre-test mode terpisah
      $posttestId   id post-test mode terpisah
--}}
@php
    $paketSama    = $paketSama ?? true;
    $paketUtamaId = $paketUtamaId ?? null;
    $pretestId    = $pretestId ?? null;
    $posttestId   = $posttestId ?? null;
@endphp

{{-- Pilihan mode: segmented pill (Soft Minimalist) --}}
<div>
    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5">Mode Paket Soal</span>
    <div class="inline-flex rounded-xl bg-slate-100 dark:bg-slate-800 p-1 gap-1" role="radiogroup" aria-label="Mode paket soal" id="paket_mode_pills">
        <input type="hidden" name="paket_sama" id="paket_sama_value" value="{{ $paketSama ? '1' : '0' }}">
        <button type="button" data-mode="1" role="radio" aria-checked="{{ $paketSama ? 'true' : 'false' }}"
                class="paket-pill px-4 py-1.5 text-xs font-semibold rounded-lg transition-all">
            Satu paket
        </button>
        <button type="button" data-mode="0" role="radio" aria-checked="{{ $paketSama ? 'false' : 'true' }}"
                class="paket-pill px-4 py-1.5 text-xs font-semibold rounded-lg transition-all">
            Bedakan Pre &amp; Post
        </button>
    </div>
    <p class="text-[11px] text-slate-400 mt-1.5">Disarankan satu paket: instrumen identik membuat skor N-Gain valid &amp; sebanding.</p>
</div>

{{-- Mode gabungan: satu dropdown --}}
<div id="paket_sama_wrap" class="{{ $paketSama ? '' : 'hidden' }}">
    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_paket_utama">
        Paket Soal Pre & Post-Test <span class="text-rose-500">*</span>
    </label>
    <x-custom-select
        name="paket_utama"
        id="f_paket_utama"
        :selected="$paketUtamaId"
        :options="$paketList ?? []"
        placeholder="Pilih paket soal…"
        :required="true" />
    @error('paket_utama')
        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
    @enderror
    <p class="text-[11px] text-slate-400 mt-1">Paket yang sama dipakai untuk asesmen awal dan evaluasi pasca materi.</p>
</div>

{{-- Mode terpisah: dua dropdown --}}
<div id="paket_beda_wrap" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $paketSama ? 'hidden' : '' }}">
    <div>
        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_pretest">
            Paket Soal Pre-Test <span class="text-rose-500">*</span>
        </label>
        <x-custom-select
            name="pretest_package_id"
            id="f_pretest"
            :selected="$pretestId"
            :options="$paketList ?? []"
            placeholder="Pilih paket soal pre-test…"
            :required="true" />
        @error('pretest_package_id')
            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
        @enderror
        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk asesmen awal sebelum sosialisasi.</p>
    </div>

    <div>
        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="f_posttest">
            Paket Soal Post-Test <span class="text-rose-500">*</span>
        </label>
        <x-custom-select
            name="posttest_package_id"
            id="f_posttest"
            :selected="$posttestId"
            :options="$paketList ?? []"
            placeholder="Pilih paket soal post-test…"
            :required="true" />
        @error('posttest_package_id')
            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
        @enderror
        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk evaluasi pemahaman pasca materi.</p>
    </div>

    {{-- Konfirmasi instrumen tidak setara (muncul setelah validasi backend menolak) --}}
    @if(session('butuh_konfirmasi_beda'))
    <div class="md:col-span-2 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-900 px-3.5 py-2.5 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <label class="flex items-start gap-2 cursor-pointer">
            <input type="checkbox" name="konfirmasi_beda" value="1" class="mt-0.5 accent-amber-600" {{ old('konfirmasi_beda') ? 'checked' : '' }}>
            <span>Jumlah soal kedua paket berbeda — N-Gain kurang valid. Centang ini lalu tekan Simpan lagi untuk tetap lanjutkan.</span>
        </label>
    </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    if (window.__paketPillsInit) return;
    window.__paketPillsInit = true;

    const ACTIVE = ['bg-white', 'dark:bg-slate-900', 'text-blue-700', 'dark:text-blue-300', 'shadow-xs'];
    const IDLE = ['text-slate-500', 'dark:text-slate-400'];

    function paint(pills, mode) {
        pills.forEach(btn => {
            const on = btn.dataset.mode === mode;
            btn.setAttribute('aria-checked', on ? 'true' : 'false');
            btn.classList.remove(...ACTIVE, ...IDLE);
            btn.classList.add(...(on ? ACTIVE : IDLE));
        });
    }

    const wrap = document.getElementById('paket_mode_pills');
    if (!wrap) return;
    const hidden = document.getElementById('paket_sama_value');
    const pills = Array.from(wrap.querySelectorAll('.paket-pill'));
    const samaWrap = document.getElementById('paket_sama_wrap');
    const bedaWrap = document.getElementById('paket_beda_wrap');

    function sync(mode) {
        hidden.value = mode;
        paint(pills, mode);
        const sama = mode === '1';
        samaWrap?.classList.toggle('hidden', !sama);
        bedaWrap?.classList.toggle('hidden', sama);
    }

    pills.forEach(btn => btn.addEventListener('click', () => sync(btn.dataset.mode)));
    sync(hidden.value === '1' ? '1' : '0');
})();
</script>
@endpush
