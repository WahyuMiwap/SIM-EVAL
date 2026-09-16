# Frontend SIM-EVAL — Dokumentasi Perubahan Kode

> **Commit:** `c32a2bb` — *refactor(frontend): code quality improvements*
> **Tanggal:** 17 September 2026

---

## Arsitektur Frontend

| Layer | Teknologi |
|---|---|
| Markup | Blade (Laravel) |
| Styling | Tailwind CSS v4 + Vanilla CSS (custom classes) |
| Font | Inter (body/UI) · Plus Jakarta Sans (heading/display) |
| Scripting | Vanilla JavaScript (IIFE + addEventListener) |
| Icons | SVG inline |

---

## Design System (`resources/css/app.css`)

### Token CSS (`@theme`)

Semua nilai desain didefinisikan sebagai CSS Custom Properties:

```
Warna     : --primary, --primary-light, --purple, --danger, --danger-light
Surface   : --bg, --bg-alt, --surface, --border, --border-strong
Teks      : --text-primary, --text-secondary, --text-muted, --text-xmuted
Radius    : --r-xs (4px) → --r-full (9999px)
Shadow    : --shadow-xs → --shadow-modal
Font      : --font-sans (Inter), --font-display (Plus Jakarta Sans)
```

### Kelas Komponen Global

| Kelas | Fungsi |
|---|---|
| `.glass`, `.glass-solid` | Card surface — border + shadow + background (keduanya identik, combined selector) |
| `.btn`, `.btn-primary`, `.btn-secondary` | Tombol |
| `.btn-sm`, `.btn-icon` | Modifier ukuran tombol |
| `.badge`, `.badge-blue`, `.badge-gray`, `.badge-green` | Label status |
| `.animate-fade-in`, `.animate-delay-*` | Animasi masuk halaman |
| `.empty-state` | State kosong (no data) |
| `.sidebar-mini` | Toggle sidebar collapse |

---

## Perubahan yang Dilakukan (Commit `c32a2bb`)

### 1. `welcome.blade.php` — Ganti Total

**Masalah sebelumnya:**
- File default Laravel yang tidak dimodifikasi
- Menggunakan font `instrument-sans` dari bunny.net — **berbeda** dari design system (Inter)
- Ada inline CSS Tailwind minified ~200KB sebagai fallback
- URL `/dashboard` hardcoded tanpa `route()`

**Solusi:** Diganti dengan halaman branded SIM-EVAL ringan (~1KB) yang langsung redirect ke dashboard via `<meta http-equiv="refresh">`.

---

### 2. `app.css` — Hapus Duplikat & Dead Code

#### Fix A: `.glass` dan `.glass-solid` digabung

```diff
- .glass { background: var(--surface); border: 1px solid var(--border); ... }
- .glass-solid { background: var(--surface); border: 1px solid var(--border); ... }  /* identik! */
+ .glass,
+ .glass-solid {
+     background: var(--surface);
+     border: 1px solid var(--border);
+     border-radius: var(--r-lg);
+     box-shadow: var(--shadow-sm);
+ }
```

#### Fix B: Token dan class font-mono dihapus

`--font-mono` berisi `Inter` (bukan font monospace) dan `.font-mono` mereferensikan `--font-sans` (salah referensi). Keduanya dihapus karena font monospace memang sudah dihapus dari design system.

```diff
- --font-mono: 'Inter', ui-sans-serif, system-ui, sans-serif;
- .font-mono { font-family: var(--font-sans); font-variant-numeric: tabular-nums; }
```

---

### 3. `bank-soal/index.blade.php` — Bersihkan Inline Style Redundan

```diff
- <div class="glass animate-fade-in"
-      style="padding:0; overflow:hidden; border-radius:var(--r-xl);
-             box-shadow:var(--shadow-sm); border:1px solid var(--border);
-             background:var(--surface);">
+ <div class="glass animate-fade-in"
+      style="padding:0; overflow:hidden; border-radius:var(--r-xl);">
```

`box-shadow`, `border`, `background` sudah disediakan `.glass`. Hanya 3 properti yang benar-benar override yang dipertahankan.

---

### 4. `bank-soal/detail.blade.php` — Hapus Tombol Edit Duplikat

Ada dua tombol "Edit Paket" ke URL yang sama — satu di header card, satu di footer card.

```diff
  <div class="flex items-center justify-start pt-5 mt-6" ...>
      <a href="..." class="btn btn-secondary">Kembali ke Daftar</a>
-     <a href="..." class="btn btn-primary">Edit Paket Ini</a>
  </div>
```

Tombol Edit tetap ada di header card. Footer hanya menampilkan "Kembali ke Daftar".

---

### 5. `kegiatan/index.blade.php` — Filter: Client-side → Server-side

Ini adalah perubahan paling signifikan secara logika.

#### Masalah Sebelumnya (Bug)

Filter (search, mode, periode) bekerja dengan **menyembunyikan `<tr>`** di DOM via JS. Karena data di-paginate dari server (misal 10/halaman), filter hanya berlaku untuk data di halaman aktif — **bukan seluruh dataset**. User mengira semua data difilter, padahal tidak.

#### Solusi: URL-based Navigation

```
Sebelum: klik "Digital" → JS menyembunyikan baris mode=kertas di DOM
Sesudah: klik "Digital" → navigate({ mode: 'digital' }) → redirect ke ?mode=digital → server filter
```

#### Filter Bar: State dari Server

Filter bar membaca state aktif dari `$filters` PHP yang dikirim server:

```blade
{{-- Active state dari server, bukan dari JS var --}}
<button class="kg-pill {{ $fMode === 'digital' ? 'active' : '' }}" data-mode="digital">Digital</button>
<input id="kgSearch" value="{{ $fSearch }}">
```

#### Hapus Semua `onclick=""` Inline

```diff
- <button onclick="togglePeriodDropdown()">Semua Waktu</button>
- <button onclick="setPeriod('7d', '7 Hari Terakhir')">7 Hari</button>
- <button onclick="setMode(this, 'digital')">Digital</button>
- <input oninput="filterTable()">

+ // Di script — pakai addEventListener:
+ periodTrigger.addEventListener('click', () => { ... });
+ document.querySelectorAll('[data-period]').forEach(btn =>
+     btn.addEventListener('click', () => navigate({ period: btn.dataset.period }))
+ );
```

#### `editKegiatan()` Kini Diimplementasi

Fungsi ini sebelumnya dipanggil via `onclick` tapi tidak pernah didefinisikan di file manapun.

```js
window.editKegiatan = function (id) {
    fetch(`/operator/kegiatan/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            // Set modal title, method PUT, populate fields
            openModal();
        });
};
```

---

### 6. `routes/web.php` — Filter Server-side Kegiatan

```php
$search   = strtolower(trim(request('search', '')));
$mode     = request('mode', '');
$period   = request('period', 'all');
$dateFrom = request('date_from', '');
$dateTo   = request('date_to', '');

$filtered = collect($mockKegiatan)->filter(function ($k) use (...) {
    if ($search && !str_contains(strtolower($k->nama_kegiatan), $search)) return false;
    if ($mode   && $k->mode !== $mode)  return false;
    // period/date range filtering...
    return true;
})->values()->all();

return view('operator.kegiatan.index', [
    'kegiatan' => makePaginator($filtered),
    'filters'  => compact('search', 'mode', 'period', 'dateFrom', 'dateTo'),
    ...
]);
```

---

## Pola JavaScript yang Digunakan

| Pola | Digunakan di |
|---|---|
| **IIFE** `(function(){...})()` | Semua script di blade |
| **`addEventListener`** | Kegiatan index, bank-soal index — tidak ada `onclick=""` |
| **URL Navigation** `navigate({params})` | Filter kegiatan |
| **Debounce** 400ms | Search input kegiatan |
| **`fetch` + JSON** | `editKegiatan()` — ambil data untuk edit modal |
| **`DOMContentLoaded`** | Topbar search |
| **Optional chaining** `?.` | Seluruh codebase JS |

---

## Struktur File View

```
resources/views/
├── welcome.blade.php              # Branded redirect ringan (~1KB)
├── layouts/app.blade.php          # Layout utama: sidebar, topbar, theme
├── components/
│   ├── sidebar.blade.php
│   ├── topbar-search.blade.php    # Global searchbar
│   └── reauth-modal.blade.php     # Modal konfirmasi hapus
└── operator/
    ├── dashboard.blade.php
    ├── profil/index.blade.php
    ├── kegiatan/
    │   ├── index.blade.php        # Filter server-side, addEventListener
    │   └── detail.blade.php       # Detail + OMR scanner
    ├── bank-soal/
    │   ├── index.blade.php        # Daftar paket soal
    │   ├── detail.blade.php       # Lihat soal + cetak A4
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── lokasi/index.blade.php
```

---

## Perubahan Halaman Dashboard (`operator/dashboard.blade.php`)

1. **Hapus Tren Evaluasi Sosialisasi**:
   - Menghapus card diagram evaluasi sosialisasi (Chart.js canvas dan pemilih diagram garis/batang).
   - Menghapus script Chart.js yang tidak lagi digunakan (~240 baris kode), sehingga halaman memuat jauh lebih ringan dan bebas error.
2. **Card Kegiatan Terbaru**:
   - Menghapus tombol/tautan `"Semua →"` pada header card kegiatan terbaru.
   - Menampilkan tepat 5 kegiatan terbaru secara terstruktur (SMAN 1 Surabaya, Kec. Tegalsari, Kel. Jambangan, Aula PDAM Sby, dan Lapas Kelas I Surabaya).
3. **Penyederhanaan Skrip**:
   - Mengganti inline `onclick="setFilterPreset(...)"` dengan `addEventListener`.
   - Mempertahankan FullCalendar dan interaktivitas filter metrik global secara modular.

---

## Hal yang Masih Bisa Diperbaiki (Future)

| Item | Keterangan |
|---|---|
| Style `kg-*` di `kegiatan/index.blade.php` | ~500 baris CSS halaman dalam `<style>` inline. Bisa dipindah ke `app.css`. |
| Campuran Tailwind utility + custom class | `class="flex items-center"` bercampur dengan `.bs-icon-box`. Tidak salah, tapi kurang konsisten. |
| Mock data di `routes/web.php` | Filter server-side sudah berfungsi, tapi perlu diganti controller nyata saat integrasi database. |

