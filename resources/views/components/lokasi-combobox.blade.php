{{--
    Combobox Lokasi Binaan — searchable + quick-add.
    Bisa ketik per huruf untuk memfilter lokasi yang sudah tersimpan
    (skalabel hingga ratusan data via endpoint AJAX), tapi nilai yang
    disubmit tetap lokasi_id sehingga relasi DB aman.

    Props:
      $name          nama hidden input (default 'lokasi_id')
      $selectedId    id terpilih (old() / model)
      $selectedLabel label terpilih untuk ditampilkan awal
      $required      bool
      $inputId       id unik bila ada >1 combobox per halaman
      $initial       array awal [{id,nama_lokasi,jenis_sasaran,kecamatan}] untuk instant display
--}}
@props([
    'name' => 'lokasi_id',
    'selectedId' => null,
    'selectedLabel' => '',
    'required' => false,
    'inputId' => 'lokasi_search',
    'initial' => [],
])

<div class="lokasi-combobox relative" data-combobox
     data-search-url="{{ route('operator.lokasi.search') }}"
     data-input-id="{{ $inputId }}"
     data-initial='@json($initial)'>
    <div class="relative">
        <input type="text"
               id="{{ $inputId }}"
               autocomplete="off"
               role="combobox" aria-expanded="false" aria-autocomplete="list"
               placeholder="Pilih atau ketik untuk mencari lokasi…"
               value="{{ $selectedLabel }}"
               @if($required) required @endif
               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-9 cursor-pointer shadow-xs">
        <input type="hidden" name="{{ $name }}" value="{{ $selectedId }}" data-lokasi-id>
        <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center transition-transform duration-200" data-combobox-caret>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </span>
    </div>

    <div data-dropdown
         class="hidden absolute z-30 mt-1.5 w-full max-h-60 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-lg text-xs py-1 divide-y divide-slate-100/70 dark:divide-slate-800/70">
    </div>
    <p class="text-[11px] text-slate-400 mt-1">Pilih dari daftar atau ketik untuk mencari nama sekolah / wilayah binaan.</p>
</div>

@push('scripts')
<script>
(function () {
    if (window.__lokasiComboboxInit) return;
    window.__lokasiComboboxInit = true;

    const TEXT_COLORS = {
        sekolah: 'color:#2563eb;',
        kampus: 'color:#7c3aed;',
        masyarakat: 'color:#059669;',
        komunitas: 'color:#059669;',
        lapas: 'color:#d97706;',
        instansi: 'color:#64748b;',
    };

    function badge(jenis) {
        const key = (jenis || 'sekolah').toLowerCase();
        const label = key.charAt(0).toUpperCase() + key.slice(1);
        return `<span style="${TEXT_COLORS[key] || TEXT_COLORS.instansi}font-size:11px;font-weight:600;white-space:nowrap;">${label}</span>`;
    }

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    document.querySelectorAll('[data-combobox]').forEach(root => {
        const searchUrl = root.dataset.searchUrl;
        const input = root.querySelector('input[type="text"]');
        const hidden = root.querySelector('[data-lokasi-id]');
        const dropdown = root.querySelector('[data-dropdown]');
        const caret = root.querySelector('[data-combobox-caret]');
        let initial = [];
        try { initial = JSON.parse(root.dataset.initial || '[]'); } catch (e) { initial = []; }

        let items = initial.slice(0, 25);
        let activeIdx = -1;
        let debounce = null;
        let lastQuery = null;

        function open() {
            // Tutup dropdown select lain jika sedang terbuka
            document.querySelectorAll('[data-select-dropdown]').forEach(d => d.classList.add('hidden'));
            document.querySelectorAll('[data-custom-select]').forEach(s => {
                s.classList.remove('z-40');
                const c = s.querySelector('[data-select-caret]');
                if (c) c.classList.remove('rotate-180');
            });

            dropdown.classList.remove('hidden');
            input.setAttribute('aria-expanded', 'true');
            root.classList.add('z-40');
            if (caret) caret.classList.add('rotate-180');
        }

        function close() {
            dropdown.classList.add('hidden');
            input.setAttribute('aria-expanded', 'false');
            activeIdx = -1;
            root.classList.remove('z-40');
            if (caret) caret.classList.remove('rotate-180');
        }

        function render(list, query) {
            if (!list.length) {
                const q = esc(query || input.value.trim());
                dropdown.innerHTML = `
                    <div class="px-3.5 py-3 text-slate-500 dark:text-slate-400">
                        <p class="font-medium">Tidak ditemukan${q ? ` untuk “${q}”` : ''}.</p>
                        ${q ? `<button type="button" data-quickadd="${q}"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-xs">
                            + Tambah “${q}” sebagai lokasi baru
                        </button>` : `<p class="mt-1 text-[11px] text-slate-400">Coba kata kunci lain.</p>`}
                    </div>`;
                open();
                return;
            }
            const curId = String(hidden.value || '');
            dropdown.innerHTML = list.map((it, i) => {
                const isSelected = String(it.id) === curId;
                return `
                <button type="button" data-idx="${i}" data-id="${it.id}"
                    class="w-full text-left px-3.5 py-2.5 flex items-center justify-between gap-2.5 transition-colors cursor-pointer group ${isSelected ? 'bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-semibold' : (i === activeIdx ? 'bg-blue-50/50 dark:bg-slate-800' : 'hover:bg-slate-50 dark:hover:bg-slate-800/80')}">
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-1.5">
                            <span class="block text-xs font-medium text-slate-700 dark:text-slate-200 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400">${esc(it.nama_lokasi)}</span>
                            ${isSelected ? `<svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>` : ''}
                        </span>
                        ${it.kecamatan ? `<span class="block text-[11px] text-slate-400 truncate mt-0.5">${esc(it.kecamatan)}</span>` : ''}
                    </span>
                    <span class="flex-shrink-0">${badge(it.jenis_sasaran)}</span>
                </button>`;
            }).join('')
            + (query ? `<div class="p-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <button type="button" data-quickadd="${esc(query)}"
                    class="w-full text-center py-1.5 px-2 rounded-lg text-xs font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition-colors">
                    + Tidak ketemu? Tambah “${esc(query)}” baru
                </button></div>` : '');
            open();
        }

        function filterClient(q) {
            q = q.toLowerCase();
            return initial.filter(it =>
                (`${it.nama_lokasi} ${it.kecamatan || ''} ${it.jenis_sasaran || ''}`.toLowerCase().includes(q))
            ).slice(0, 20);
        }

        async function doSearch(q) {
            // Query pendek: filter lokal (instan, tanpa request)
            if (q.length < 2) { render(filterClient(q), q); return; }
            // Query panjang: AJAX agar skalabel ke ratusan data
            try {
                const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}&limit=20`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('search failed');
                const json = await res.json();
                // Samakan bentuk dengan initial
                items = (json.data || []).map(d => ({
                    id: d.id, nama_lokasi: d.nama_lokasi,
                    kecamatan: d.kecamatan, jenis_sasaran: d.jenis_sasaran,
                }));
                render(items, q);
            } catch (e) {
                render(filterClient(q), q);
            }
        }

        function scheduleSearch(q) {
            clearTimeout(debounce);
            if (q === lastQuery) return;
            debounce = setTimeout(() => { lastQuery = q; doSearch(q); }, 200);
        }

        // Pilih item → isi hidden lokasi_id (relasi aman)
        function selectByIdx(i) {
            const it = items[i];
            if (!it) return;
            hidden.value = it.id;
            input.value = `${it.nama_lokasi} (${capitalize(it.jenis_sasaran)})`;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            close();
        }
        function capitalize(s) { s = String(s || 'sekolah'); return s.charAt(0).toUpperCase() + s.slice(1); }

        input.addEventListener('focus', () => {
            items = initial.slice(0, 20);
            render(items, '');
        });
        input.addEventListener('input', () => {
            // User mengetik ulang → reset pilihan lama agar tidak kirim id basi
            hidden.value = '';
            scheduleSearch(input.value.trim());
        });
        input.addEventListener('keydown', (e) => {
            if (dropdown.classList.contains('hidden')) return;
            const buttons = dropdown.querySelectorAll('[data-idx]');
            if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, buttons.length - 1); render(items, input.value.trim()); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, 0); render(items, input.value.trim()); }
            else if (e.key === 'Enter') {
                if (activeIdx >= 0) { e.preventDefault(); selectByIdx(activeIdx); }
                // Enter tanpa pilihan: biarkan required/hidden validation bekerja
            }
            else if (e.key === 'Escape') { close(); }
        });

        dropdown.addEventListener('click', (e) => {
            const pick = e.target.closest('[data-idx]');
            if (pick) {
                const id = pick.dataset.id;
                const idx = items.findIndex(it => String(it.id) === String(id));
                if (idx >= 0) { activeIdx = idx; selectByIdx(idx); }
                return;
            }
            const qa = e.target.closest('[data-quickadd]');
            if (qa) {
                const nama = qa.dataset.quickadd || input.value.trim();
                if (window.openLokasiQuickAdd) window.openLokasiQuickAdd(nama, root);
            }
        });

        document.addEventListener('click', (e) => {
            if (!root.contains(e.target)) close();
        });

        // Dipakai modal quick-add setelah sukses: set pilihan baru
        root._setLokasi = function (obj) {
            items = [{ id: obj.id, nama_lokasi: obj.nama_lokasi, kecamatan: obj.kecamatan, jenis_sasaran: obj.jenis_sasaran }];
            hidden.value = obj.id;
            input.value = obj.label || `${obj.nama_lokasi} (${capitalize(obj.jenis_sasaran)})`;
            // Tambahkan ke cache lokal agar langsung bisa dicari lagi
            initial.unshift(items[0]);
            try { root.dataset.initial = JSON.stringify(initial.slice(0, 100)); } catch (e) {}
            close();
        };
    });
})();
</script>
@endpush
