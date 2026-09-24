/**
 * submitGuard.js — Anti double-submit + loading state untuk semua form.
 * Pakai: guardForm('kegiatanForm'); guardAllForms();
 */

export function guardForm(formId, btnSelector = '[type=submit]') {
    const form = document.getElementById(formId);
    if (!form || form.dataset.guarded) return;
    form.dataset.guarded = '1';
    form.addEventListener('submit', () => {
        const btns = form.querySelectorAll(btnSelector);
        btns.forEach((b) => {
            if (b.disabled) return;
            b.disabled = true;
            if (!b.dataset.label) b.dataset.label = b.innerHTML;
            b.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span> Memproses…';
        });
        setTimeout(() => {
            btns.forEach((b) => {
                b.disabled = false;
                if (b.dataset.label) b.innerHTML = b.dataset.label;
            });
        }, 8000);
    });
}

export function guardAllForms() {
    ['kegiatanForm', 'lokasiForm', 'soalForm', 'formResetPassword', 'formEditUser', 'formAddUser', 'pfInfoForm', 'pfPasswordForm', 'joinForm', 'quizForm'].forEach((id) => guardForm(id));
}

if (typeof window !== 'undefined') {
    window.guardForm = guardForm;
    window.guardAllForms = guardAllForms;
}
