@extends('layouts.app')

@section('page-title', 'Profil Operator')
@section('page-subtitle', 'Kelola informasi akun dan keamanan akun Seksi P2M')

@section('content')

{{-- ── Page Header ────────────────────────────────────────────── --}}
<div class="pf-page-header">
    <div>
        <h2 class="pf-heading">Profil Saya</h2>
        <p class="pf-subheading">Informasi akun kedinasan dan pengelolaan keamanan</p>
    </div>
</div>

{{-- ── Main Grid ───────────────────────────────────────────────── --}}
<div class="pf-grid">

    {{-- ── LEFT COLUMN: Identity Card ────────────────────────────── --}}
    <div class="pf-col-left">

        <div class="pf-card pf-avatar-card">
            {{-- Avatar Display --}}
            <div class="pf-avatar-section">
                <div class="pf-avatar-ring" id="pfAvatarRing">
                    <div class="pf-avatar" id="pfAvatarDisplay">
                        <img id="pfAvatarImg" src="" alt="Foto Profil" class="pf-avatar-img pf-hidden">
                        <span id="pfAvatarInitial" class="pf-avatar-initial">W</span>
                    </div>
                    {{-- Upload overlay (hover) --}}
                    <label for="pfAvatarInput" class="pf-avatar-overlay" title="Ganti foto profil">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Ganti Foto</span>
                    </label>
                    <input type="file" id="pfAvatarInput" accept="image/*" class="pf-sr-only">
                </div>

                <div class="pf-avatar-info">
                    <h3 class="pf-avatar-name" id="pfDisplayName">Wahyu</h3>
                    <p class="pf-avatar-role" id="pfDisplayJabatan">Penyuluh Narkoba Ahli Pertama</p>
                    <span class="badge badge-blue">Seksi P2M &middot; BNN Kota Surabaya</span>
                </div>
            </div>

            {{-- Upload hint --}}
            <p class="pf-upload-hint">
                Format JPG/PNG &middot; Maks. 2 MB
            </p>

            {{-- Quick Identity Details --}}
            <div class="pf-quick-info">
                <div class="pf-quick-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="truncate" id="pfDisplayEmail">wahyu@bnnsurabaya.go.id</span>
                </div>
                <div class="pf-quick-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    <span>NIP: <span class="font-medium" id="pfDisplayNip">199203142015041001</span></span>
                </div>
                <div class="pf-quick-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Seksi P2M (Pencegahan)</span>
                </div>
            </div>
        </div>

    </div>{{-- /col-left --}}

    {{-- ── RIGHT COLUMN: Forms ────────────────────────────────────── --}}
    <div class="pf-col-right">

        {{-- Tab Navigation --}}
        <div class="pf-tabs">
            <button class="pf-tab active" id="pfTabInfo" onclick="pfSwitchTab('info')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Informasi Akun
            </button>
            <button class="pf-tab" id="pfTabPassword" onclick="pfSwitchTab('password')">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Keamanan &amp; Password
            </button>
        </div>

        {{-- ── TAB 1: Informasi Akun ────────────────────────────── --}}
        <div class="pf-card pf-form-card" id="pfPanelInfo">

            <div class="pf-card-header">
                <div>
                    <h3 class="pf-card-title">Data Pegawai / Operator</h3>
                    <p class="pf-card-desc">Perbarui data identitas operator P2M yang digunakan pada sistem SIM-EVAL</p>
                </div>
            </div>

            <form id="pfInfoForm" onsubmit="pfSaveInfo(event)">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="form-group">
                    <label class="form-label" for="pf_nama">
                        Nama Lengkap <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="text" id="pf_nama" name="nama" class="form-input"
                        value="Staf P2M Demo" placeholder="Masukkan nama lengkap Anda" required>
                    <p class="pf-field-hint">Nama ini dicantumkan sebagai penanggung jawab penginputan kegiatan.</p>
                </div>

                {{-- Grid 2 col: NIP & Email --}}
                <div class="pf-form-grid">
                    {{-- NIP --}}
                    <div class="form-group">
                        <label class="form-label" for="pf_nip">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" id="pf_nip" name="nip" class="form-input"
                            value="199203142015041001" placeholder="18 digit angka NIP"
                            inputmode="numeric" maxlength="20">
                        <p class="pf-field-hint">Identitas kedinasan BNN</p>
                    </div>

                    {{-- Email Akun --}}
                    <div class="form-group">
                        <label class="form-label" for="pf_email">
                            Email Akun <span style="color:var(--danger)">*</span>
                        </label>
                        <div class="pf-input-icon-wrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="pf-input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input type="email" id="pf_email" name="email" class="form-input pf-input-has-icon"
                                value="staf@bnnsurabaya.go.id" placeholder="nama@bnnsurabaya.go.id" required>
                        </div>
                        <p class="pf-field-hint">Digunakan untuk autentikasi login</p>
                    </div>
                </div>

                {{-- Grid 2 col: Jabatan & Unit Kerja --}}
                <div class="pf-form-grid">
                    {{-- Jabatan --}}
                    <div class="form-group">
                        <label class="form-label" for="pf_jabatan">Jabatan</label>
                        <input type="text" id="pf_jabatan" name="jabatan" class="form-input"
                            value="Penyuluh Narkoba Ahli Pertama" placeholder="Contoh: Penyuluh Narkoba / Staf P2M">
                    </div>

                    {{-- Unit Kerja (Khas P2M) --}}
                    <div class="form-group">
                        <label class="form-label" for="pf_unit">Unit Kerja</label>
                        <input type="text" id="pf_unit" name="unit" class="form-input pf-readonly-field"
                            value="Seksi Pencegahan & Pemberdayaan Masyarakat (P2M)" readonly>
                        <p class="pf-field-hint">Terkunci &middot; Aplikasi khusus internal Seksi P2M</p>
                    </div>
                </div>

                <div class="pf-form-actions">
                    <button type="button" class="btn btn-secondary" onclick="pfResetInfo()">Reset</button>
                    <button type="submit" class="btn btn-primary" id="pfInfoSaveBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ── TAB 2: Ganti Password ─────────────────────────────── --}}
        <div class="pf-card pf-form-card pf-hidden" id="pfPanelPassword">

            <div class="pf-card-header">
                <div>
                    <h3 class="pf-card-title">Keamanan Akun</h3>
                    <p class="pf-card-desc">Ganti password secara berkala untuk menjaga kerahasiaan data evaluasi P2M</p>
                </div>
            </div>

            {{-- Tips --}}
            <div class="pf-security-tips">
                <div class="pf-tip-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="pf-tip-title">Tips password aman</p>
                    <p class="pf-tip-text">Gunakan minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, dan angka.</p>
                </div>
            </div>

            <form id="pfPasswordForm" onsubmit="pfSavePassword(event)">
                @csrf

                {{-- Password Lama --}}
                <div class="form-group">
                    <label class="form-label" for="pf_oldpass">Password Saat Ini <span style="color:var(--danger)">*</span></label>
                    <div class="pf-password-wrap">
                        <input type="password" id="pf_oldpass" name="current_password" class="form-input pf-password-input"
                            placeholder="Masukkan password Anda saat ini" required>
                        <button type="button" class="pf-pw-toggle" onclick="pfTogglePassword('pf_oldpass',this)" title="Lihat/Sembunyikan password">
                            <svg class="pf-eye pf-eye-on" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pf-eye pf-eye-off pf-hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Password Baru --}}
                <div class="form-group">
                    <label class="form-label" for="pf_newpass">Password Baru <span style="color:var(--danger)">*</span></label>
                    <div class="pf-password-wrap">
                        <input type="password" id="pf_newpass" name="password" class="form-input pf-password-input"
                            placeholder="Min. 8 karakter" required oninput="pfCheckStrength(this.value)">
                        <button type="button" class="pf-pw-toggle" onclick="pfTogglePassword('pf_newpass',this)" title="Lihat/Sembunyikan password">
                            <svg class="pf-eye pf-eye-on" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pf-eye pf-eye-off pf-hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <div class="pf-strength-wrap">
                        <div class="pf-strength-bar"><div class="pf-strength-fill" id="pfStrengthFill"></div></div>
                        <span class="pf-strength-label" id="pfStrengthLabel">—</span>
                    </div>
                </div>

                {{-- Konfirmasi --}}
                <div class="form-group">
                    <label class="form-label" for="pf_confirmpass">Konfirmasi Password Baru <span style="color:var(--danger)">*</span></label>
                    <div class="pf-password-wrap">
                        <input type="password" id="pf_confirmpass" name="password_confirmation" class="form-input pf-password-input"
                            placeholder="Ulangi password baru" required oninput="pfCheckMatch()">
                        <button type="button" class="pf-pw-toggle" onclick="pfTogglePassword('pf_confirmpass',this)" title="Lihat/Sembunyikan password">
                            <svg class="pf-eye pf-eye-on" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="pf-eye pf-eye-off pf-hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <p class="pf-match-hint pf-hidden" id="pfMatchError" style="color:var(--danger)">Password tidak cocok</p>
                    <p class="pf-match-hint pf-hidden" id="pfMatchOk" style="color:var(--success)">&#10003; Password cocok</p>
                </div>

                <div class="pf-form-actions">
                    <button type="button" class="btn btn-secondary" onclick="pfResetPassword()">Reset</button>
                    <button type="submit" class="btn btn-primary" id="pfPwSaveBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>

    </div>{{-- /col-right --}}
</div>{{-- /pf-grid --}}

{{-- Toast Container --}}
<div id="pfToastContainer"></div>

{{-- ── Styles ─────────────────────────────────────────────────── --}}
<style>
.pf-hidden { display: none !important; }
.pf-sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }
.pf-mono { font-family: var(--font-sans) !important; font-variant-numeric: tabular-nums; }

/* Page Header */
.pf-page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.75rem; gap: 1rem; flex-wrap: wrap; }
.pf-heading { font-family: var(--font-display); font-size: 1.375rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.025em; margin: 0 0 0.2rem; }
.pf-subheading { font-size: 0.875rem; color: var(--text-muted); margin: 0; }
.pf-header-badge { display: inline-flex; align-items: center; gap: 0.35rem; background: var(--success-light); color: #15803D; font-size: 0.75rem; font-weight: 600; padding: 0.3rem 0.75rem; border-radius: var(--r-full); white-space: nowrap; flex-shrink: 0; }

/* Layout */
.pf-grid { display: grid; grid-template-columns: 310px 1fr; gap: 1.5rem; align-items: start; }
@media (max-width: 900px) { .pf-grid { grid-template-columns: 1fr; } }
.pf-col-left { display: flex; flex-direction: column; gap: 1rem; }
.pf-col-right { display: flex; flex-direction: column; }

/* Cards */
.pf-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-xl); box-shadow: var(--shadow-sm); }
.pf-avatar-card { padding: 1.75rem; }

/* Avatar */
.pf-avatar-section { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.875rem; margin-bottom: 0.875rem; }
.pf-avatar-ring { position: relative; flex-shrink: 0; }
.pf-avatar {
    width: 96px; height: 96px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden;
    box-shadow: 0 4px 20px rgba(67,97,238,0.28);
    border: 3px solid var(--surface);
    outline: 2.5px solid rgba(67,97,238,0.35);
}
.pf-avatar-img { width: 100%; height: 100%; object-fit: cover; }
.pf-avatar-initial { font-family: var(--font-display); font-size: 2.5rem; font-weight: 800; color: #fff; line-height: 1; letter-spacing: -0.02em; }
.pf-avatar-overlay {
    position: absolute; inset: 0; border-radius: 50%;
    background: rgba(17,24,39,0.65); display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 0.2rem;
    color: #fff; font-size: 0.65rem; font-weight: 600;
    opacity: 0; cursor: pointer; transition: opacity 0.2s ease; backdrop-filter: blur(2px);
}
.pf-avatar-ring:hover .pf-avatar-overlay { opacity: 1; }
.pf-avatar-info { display: flex; flex-direction: column; align-items: center; gap: 0.35rem; }
.pf-avatar-name { font-family: var(--font-display); font-size: 1.0625rem; font-weight: 700; color: var(--text-primary); margin: 0; letter-spacing: -0.015em; text-align: center; }
.pf-avatar-role { font-size: 0.8125rem; color: var(--text-muted); margin: 0; text-align: center; }
.pf-upload-hint { text-align: center; font-size: 0.72rem; color: var(--text-muted); margin: 0 0 1.25rem; }

/* Quick Info */
.pf-quick-info { display: flex; flex-direction: column; gap: 0.75rem; padding-top: 1.125rem; border-top: 1px solid var(--border); }
.pf-quick-item { display: flex; align-items: center; gap: 0.625rem; font-size: 0.8125rem; color: var(--text-secondary); }
.pf-quick-item svg { color: var(--text-muted); flex-shrink: 0; }

/* Scope Box */
.pf-scope-box {
    margin-top: 1.25rem;
    padding: 0.75rem 0.875rem;
    background: var(--bg-alt);
    border: 1px dashed var(--border);
    border-radius: var(--r-md);
    font-size: 0.75rem;
    color: var(--text-muted);
    line-height: 1.45;
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
}

/* Tabs */
.pf-tabs { display: flex; gap: 0.25rem; background: var(--bg-alt); border-radius: var(--r-lg) var(--r-lg) 0 0; padding: 0.4rem; border: 1px solid var(--border); border-bottom: none; }
.pf-tab { display: flex; align-items: center; gap: 0.5rem; flex: 1; justify-content: center; padding: 0.6rem 1rem; font-size: 0.8375rem; font-weight: 600; color: var(--text-muted); background: transparent; border: none; border-radius: var(--r-md); cursor: pointer; font-family: var(--font-sans); transition: all 0.18s ease; }
.pf-tab:hover { color: var(--text-secondary); background: rgba(255,255,255,0.6); }
.pf-tab.active { background: var(--surface); color: var(--primary); box-shadow: var(--shadow-xs); }
.pf-tab svg { opacity: 0.7; }
.pf-tab.active svg { opacity: 1; }

/* Form Card */
.pf-form-card { padding: 1.75rem; border-radius: 0 0 var(--r-xl) var(--r-xl); }
.pf-card-header { margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
.pf-card-title { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 0.2rem; }
.pf-card-desc { font-size: 0.8125rem; color: var(--text-muted); margin: 0; }
.pf-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1.25rem; }
@media (max-width: 650px) { .pf-form-grid { grid-template-columns: 1fr; } }
.pf-form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border); margin-top: 1.5rem; }
.pf-field-hint { font-size: 0.72rem; color: var(--text-muted); margin: 0.3rem 0 0; }

/* Readonly input styling */
.pf-readonly-field {
    background-color: var(--bg-alt) !important;
    color: var(--text-secondary) !important;
    cursor: not-allowed;
    border-color: var(--border) !important;
}

/* Input with icon */
.pf-input-icon-wrap { position: relative; }
.pf-input-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted); pointer-events: none; }
.pf-input-has-icon { padding-left: 2.25rem !important; }

/* Password */
.pf-password-wrap { position: relative; display: flex; align-items: center; }
.pf-password-input { padding-right: 2.75rem !important; }
.pf-pw-toggle { position: absolute; right: 0.625rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem; border-radius: var(--r-xs); display: flex; align-items: center; justify-content: center; transition: color 0.15s ease; }
.pf-pw-toggle:hover { color: var(--text-primary); }
.pf-eye { width: 16px; height: 16px; }

/* Strength */
.pf-strength-wrap { display: flex; align-items: center; gap: 0.625rem; margin-top: 0.5rem; }
.pf-strength-bar { flex: 1; height: 4px; background: var(--bg-alt); border-radius: var(--r-full); overflow: hidden; }
.pf-strength-fill { height: 100%; width: 0%; border-radius: var(--r-full); transition: width 0.3s ease, background 0.3s ease; background: var(--danger); }
.pf-strength-label { font-size: 0.72rem; font-weight: 600; color: var(--text-muted); min-width: 52px; text-align: right; transition: color 0.2s ease; }
.pf-match-hint { font-size: 0.78rem; margin: 0.35rem 0 0; }

/* Tips */
.pf-security-tips { display: flex; gap: 0.75rem; background: var(--primary-light); border: 1px solid rgba(67,97,238,0.15); border-radius: var(--r-md); padding: 0.875rem 1rem; margin-bottom: 1.25rem; }
.pf-tip-icon { color: var(--primary); flex-shrink: 0; margin-top: 0.1rem; }
.pf-tip-title { font-size: 0.8125rem; font-weight: 600; color: var(--primary); margin: 0 0 0.2rem; }
.pf-tip-text { font-size: 0.78rem; color: var(--text-secondary); margin: 0; }

/* Toast */
#pfToastContainer { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; pointer-events: none; }
.pf-toast { display: flex; align-items: center; gap: 0.625rem; padding: 0.7rem 1rem; border-radius: var(--r-md); font-size: 0.8125rem; font-weight: 500; color: #fff; min-width: 240px; max-width: 340px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); pointer-events: auto; animation: pfToastIn 0.22s cubic-bezier(0.34,1.26,0.64,1) forwards; transition: opacity 0.25s ease, transform 0.25s ease; }
.pf-toast.out { opacity: 0; transform: translateX(8px); }
.pf-toast.success { background: #10b981; }
.pf-toast.error   { background: var(--danger); }
@keyframes pfToastIn { from { opacity: 0; transform: translateX(12px); } to { opacity: 1; transform: translateX(0); } }

/* Spin */
.pf-spin { animation: pfSpinAnim 0.8s linear infinite; }
@keyframes pfSpinAnim { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

{{-- ── Scripts ─────────────────────────────────────────────────── --}}
<script>
(function() {
    // ── Avatar Preview ──────────────────────────────────────────
    document.getElementById('pfAvatarInput')?.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { pfToast('Ukuran file melebihi 2 MB', 'error'); this.value = ''; return; }
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.getElementById('pfAvatarImg');
            const init = document.getElementById('pfAvatarInitial');
            img.src = e.target.result;
            img.classList.remove('pf-hidden');
            init.classList.add('pf-hidden');
            pfToast('Foto profil siap disimpan');
        };
        reader.readAsDataURL(file);
    });

    // ── Tab Switching ───────────────────────────────────────────
    window.pfSwitchTab = function(tab) {
        ['info','password'].forEach(t => {
            const panel = document.getElementById('pfPanel' + t.charAt(0).toUpperCase() + t.slice(1));
            const btn   = document.getElementById('pfTab'   + t.charAt(0).toUpperCase() + t.slice(1));
            if (t === tab) { panel?.classList.remove('pf-hidden'); btn?.classList.add('active'); }
            else           { panel?.classList.add('pf-hidden');    btn?.classList.remove('active'); }
        });
    };

    // ── Save Info ───────────────────────────────────────────────
    window.pfSaveInfo = function(e) {
        e.preventDefault();
        const nama    = document.getElementById('pf_nama').value.trim();
        const nip     = document.getElementById('pf_nip').value.trim();
        const email   = document.getElementById('pf_email').value.trim();
        const jabatan = document.getElementById('pf_jabatan').value.trim();

        const btn = document.getElementById('pfInfoSaveBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="pf-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Menyimpan...`;

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Perubahan`;
            
            // Sync to Left Card
            if (nama) {
                document.getElementById('pfDisplayName').textContent = nama;
                document.getElementById('pfAvatarInitial').textContent = nama.charAt(0).toUpperCase();
            }
            if (nip) document.getElementById('pfDisplayNip').textContent = nip;
            if (email) document.getElementById('pfDisplayEmail').textContent = email;
            if (jabatan) document.getElementById('pfDisplayJabatan').textContent = jabatan;

            pfToast('Informasi akun berhasil diperbarui!');
        }, 800);
    };

    window.pfResetInfo = function() {
        document.getElementById('pfInfoForm')?.reset();
    };

    // ── Save Password ───────────────────────────────────────────
    window.pfSavePassword = function(e) {
        e.preventDefault();
        const np = document.getElementById('pf_newpass').value;
        const cp = document.getElementById('pf_confirmpass').value;
        if (np !== cp) { pfToast('Password baru dan konfirmasi tidak cocok.', 'error'); return; }
        if (np.length < 8) { pfToast('Password minimal 8 karakter.', 'error'); return; }

        const btn = document.getElementById('pfPwSaveBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="pf-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Memperbarui...`;

        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Perbarui Password`;
            document.getElementById('pfPasswordForm')?.reset();
            pfResetPassword();
            pfToast('Password akun berhasil diperbarui!');
        }, 800);
    };

    window.pfResetPassword = function() {
        document.getElementById('pfPasswordForm')?.reset();
        document.getElementById('pfStrengthFill').style.width = '0%';
        document.getElementById('pfStrengthFill').style.background = '';
        document.getElementById('pfStrengthLabel').textContent = '—';
        document.getElementById('pfStrengthLabel').style.color = '';
        document.getElementById('pfMatchError').classList.add('pf-hidden');
        document.getElementById('pfMatchOk').classList.add('pf-hidden');
    };

    // ── Toggle Password ─────────────────────────────────────────
    window.pfTogglePassword = function(id, btn) {
        const input = document.getElementById(id);
        const on  = btn.querySelector('.pf-eye-on');
        const off = btn.querySelector('.pf-eye-off');
        if (input.type === 'password') {
            input.type = 'text';
            on.classList.add('pf-hidden'); off.classList.remove('pf-hidden');
        } else {
            input.type = 'password';
            on.classList.remove('pf-hidden'); off.classList.add('pf-hidden');
        }
    };

    // ── Password Strength ───────────────────────────────────────
    window.pfCheckStrength = function(val) {
        const fill  = document.getElementById('pfStrengthFill');
        const label = document.getElementById('pfStrengthLabel');
        if (!fill || !label) return;
        let score = 0;
        if (val.length >= 8)         score++;
        if (val.length >= 12)        score++;
        if (/[A-Z]/.test(val))       score++;
        if (/[0-9]/.test(val))       score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { max:1, pct:'20%', color:'#EF4444', lbl:'Sangat Lemah' },
            { max:2, pct:'40%', color:'#F59E0B', lbl:'Lemah' },
            { max:3, pct:'60%', color:'#EAB308', lbl:'Sedang' },
            { max:4, pct:'80%', color:'#22C55E', lbl:'Kuat' },
            { max:5, pct:'100%',color:'#16A34A', lbl:'Sangat Kuat' },
        ];
        const lv = levels.find(l => score <= l.max) || levels[4];
        fill.style.width = val.length === 0 ? '0%' : lv.pct;
        fill.style.background = lv.color;
        label.textContent = val.length === 0 ? '—' : lv.lbl;
        label.style.color = val.length === 0 ? '' : lv.color;
    };

    // ── Password Match ──────────────────────────────────────────
    window.pfCheckMatch = function() {
        const np  = document.getElementById('pf_newpass')?.value || '';
        const cp  = document.getElementById('pf_confirmpass')?.value || '';
        const err = document.getElementById('pfMatchError');
        const ok  = document.getElementById('pfMatchOk');
        if (!cp) { err?.classList.add('pf-hidden'); ok?.classList.add('pf-hidden'); return; }
        if (np === cp) { err?.classList.add('pf-hidden'); ok?.classList.remove('pf-hidden'); }
        else           { ok?.classList.add('pf-hidden');  err?.classList.remove('pf-hidden'); }
    };

    // ── Toast ───────────────────────────────────────────────────
    function pfToast(msg, type = 'success') {
        const c = document.getElementById('pfToastContainer');
        if (!c) return;
        const t = document.createElement('div');
        t.className = `pf-toast ${type}`;
        const icon = type === 'success'
            ? `<svg style="width:15px;height:15px;flex-shrink:0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`
            : `<svg style="width:15px;height:15px;flex-shrink:0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        t.innerHTML = `${icon}<span>${msg}</span>`;
        c.appendChild(t);
        setTimeout(() => { t.classList.add('out'); setTimeout(() => t.remove(), 280); }, 3000);
    }
})();
</script>

@endsection
