{{-- Dropdown Component (vanilla JS, tanpa Alpine) --}}
{{-- Props: $align = 'right', $width = 'w-72', $class = '' --}}
{{-- Slots: trigger, content --}}
<div class="relative inline-block" data-dropdown>
    <div data-dropdown-trigger class="cursor-pointer">{{ $trigger }}</div>
    <div
        data-dropdown-menu
        hidden
        class="absolute z-50 mt-2 {{ $width }} bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg {{ $align === 'right' ? 'right-0' : 'left-0' }} {{ $class }}"
    >
        <div class="py-1">{{ $content }}</div>
    </div>
</div>
<script>
(function () {
    if (window.__dropdownInit) return;
    window.__dropdownInit = true;
    document.addEventListener('click', function (e) {
        document.querySelectorAll('[data-dropdown]').forEach(function (root) {
            var trigger = root.querySelector('[data-dropdown-trigger]');
            var menu = root.querySelector('[data-dropdown-menu]');
            if (!trigger || !menu) return;
            if (trigger.contains(e.target)) {
                menu.hidden = !menu.hidden;
            } else if (!menu.contains(e.target)) {
                menu.hidden = true;
            }
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.querySelectorAll('[data-dropdown-menu]').forEach(function (m) { m.hidden = true; });
    });
})();
</script>
