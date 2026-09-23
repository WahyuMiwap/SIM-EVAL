{{--
    Modal Quick-Add Lokasi — dipakai dari combobox ("Tambah ... sebagai lokasi baru").
    Submit via fetch JSON ke operator.lokasi.store, tanpa pindah halaman.
    Bila nama duplikat → tampilkan saran "Maksudmu ...? [Pilih ini]".
--}}
<div id="lokasiQuickAddOverlay" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(15,23,42,.5);backdrop-filter:blur(2px);">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Tambah Lokasi Baru</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Langsung tersimpan ke Master Lokasi & terpilih otomatis.</p>
            </div>
            <button type="button" id="lokasiQuickAddClose"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="lokasiQuickAddForm" class="px-5 py-4 space-y-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="qa_nama">Nama Lokasi <span class="text-rose-500">*</span></label>
                <input type="text" id="qa_nama" name="nama_lokasi" required maxlength="255"
                       placeholder="cth: SMAN 20 Surabaya"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="qa_jenis">Jenis Sasaran</label>
                    <div class="relative">
                        <select id="qa_jenis" name="jenis_sasaran"
                                class="w-full appearance-none px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-9 cursor-pointer shadow-xs">
                            <option value="sekolah">Sekolah</option>
                            <option value="kampus">Kampus</option>
                            <option value="masyarakat">Masyarakat</option>
                            <option value="komunitas">Komunitas</option>
                            <option value="lapas">Lapas</option>
                            <option value="instansi">Instansi</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="qa_kecamatan">Kecamatan</label>
                    <input type="text" id="qa_kecamatan" name="kecamatan" maxlength="100" placeholder="cth: Genteng"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-700 dark:text-slate-300 block mb-1.5" for="qa_alamat">Alamat (opsional)</label>
                <input type="text" id="qa_alamat" name="alamat" maxlength="255" placeholder="Jl. contoh No. 1"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            <div id="qa_duplicate" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-3.5 py-2.5 text-xs text-amber-800"></div>
            <p id="qa_error" class="hidden text-[11px] text-rose-500 font-medium"></p>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" id="lokasiQuickAddCancel" class="px-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Batal</button>
                <button type="submit" id="qa_submit" class="px-5 py-2 text-xs font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors disabled:opacity-60">Simpan & Pilih</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    if (window.openLokasiQuickAdd) return;
    const overlay = document.getElementById('lokasiQuickAddOverlay');
    const form = document.getElementById('lokasiQuickAddForm');
    const namaInput = document.getElementById('qa_nama');
    const dupBox = document.getElementById('qa_duplicate');
    const errBox = document.getElementById('qa_error');
    const submitBtn = document.getElementById('qa_submit');
    let ownerRoot = null;

    const storeUrl = "{{ route('operator.lokasi.store') }}";
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || form.querySelector('input[name="_token"]')?.value || '';

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    window.openLokasiQuickAdd = function (nama, root) {
        ownerRoot = root || null;
        namaInput.value = nama || '';
        dupBox.classList.add('hidden'); dupBox.innerHTML = '';
        errBox.classList.add('hidden'); errBox.textContent = '';
        overlay.classList.remove('hidden');
        setTimeout(() => namaInput.focus(), 50);
    };

    function close() { overlay.classList.add('hidden'); ownerRoot = null; }
    document.getElementById('lokasiQuickAddClose').addEventListener('click', close);
    document.getElementById('lokasiQuickAddCancel').addEventListener('click', close);
    overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !overlay.classList.contains('hidden')) close(); });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errBox.classList.add('hidden');
        dupBox.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan…';
        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: new FormData(form),
            });
            const json = await res.json().catch(() => ({}));
            if (res.status === 201 && json.data) {
                if (ownerRoot && ownerRoot._setLokasi) ownerRoot._setLokasi(json.data);
                close();
                return;
            }
            if (res.status === 422 && json.existing) {
                const ex = json.existing;
                dupBox.innerHTML = `Sudah ada: <strong>${esc(ex.nama_lokasi)}</strong> (${esc(ex.jenis_sasaran)}). `
                    + `<button type="button" id="qa_pick_existing" class="font-bold underline">Maksudmu ini? Pilih →</button>`;
                dupBox.classList.remove('hidden');
                document.getElementById('qa_pick_existing').addEventListener('click', () => {
                    if (ownerRoot && ownerRoot._setLokasi) ownerRoot._setLokasi(ex);
                    close();
                });
                return;
            }
            // Validasi Laravel standar
            const msg = json.message || (json.errors ? Object.values(json.errors).flat().join(' ') : 'Gagal menyimpan lokasi.');
            errBox.textContent = msg;
            errBox.classList.remove('hidden');
        } catch (err) {
            errBox.textContent = 'Jaringan bermasalah. Coba lagi.';
            errBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan & Pilih';
        }
    });
})();
</script>
@endpush
