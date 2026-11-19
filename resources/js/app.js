import './bootstrap';

/* ---------------------------------------------------------------
 | Toast notifications: auto-dismiss after 6s, or close with the X.
 | Markup lives in resources/views/components/toast.blade.php
 --------------------------------------------------------------- */
document.querySelectorAll('[data-toast]').forEach((toast) => {
    const dismiss = () => toast.remove();
    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    setTimeout(dismiss, 6000);
});

/* ---------------------------------------------------------------
 | Mobile navigation toggle: <button data-nav-toggle="id"> shows/hides #id
 --------------------------------------------------------------- */
document.querySelectorAll('[data-nav-toggle]').forEach((button) => {
    const target = document.getElementById(button.dataset.navToggle);
    if (!target) return;

    button.addEventListener('click', () => {
        const open = target.classList.toggle('hidden') === false;
        button.setAttribute('aria-expanded', String(open));
    });
});

/* ---------------------------------------------------------------
 | Dropdown toggle: <button data-dropdown-toggle="id"> shows/hides #id
 | Auto-closes when clicking outside or pressing Escape.
 --------------------------------------------------------------- */
document.querySelectorAll('[data-dropdown-toggle]').forEach((button) => {
    const target = document.getElementById(button.dataset.dropdownToggle);
    if (!target) return;

    button.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = target.classList.toggle('hidden') === false;
        button.setAttribute('aria-expanded', String(open));
    });

    document.addEventListener('click', (e) => {
        if (!target.classList.contains('hidden') && !target.contains(e.target) && !button.contains(e.target)) {
            target.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !target.classList.contains('hidden')) {
            target.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
            button.focus();
        }
    });
});

/* ---------------------------------------------------------------
 | Tab switcher: <button data-tab-target="tab-id">
 --------------------------------------------------------------- */
document.querySelectorAll('[data-tab-target]').forEach((tab) => {
    tab.addEventListener('click', () => {
        const targetId = tab.dataset.tabTarget;
        const targetEl = document.getElementById(targetId);
        if (!targetEl) return;

        const parent = tab.closest('[role="tablist"]');
        if (parent) {
            parent.querySelectorAll('[data-tab-target]').forEach((sibling) => {
                sibling.setAttribute('aria-selected', 'false');
                sibling.classList.remove('bg-white', 'text-brand-700', 'shadow-sm', 'font-semibold');
                sibling.classList.add('text-slate-600', 'font-medium');

                const siblingTarget = document.getElementById(sibling.dataset.tabTarget);
                if (siblingTarget) siblingTarget.classList.add('hidden');
            });
        }

        tab.setAttribute('aria-selected', 'true');
        tab.classList.add('bg-white', 'text-brand-700', 'shadow-sm', 'font-semibold');
        tab.classList.remove('text-slate-600', 'font-medium');

        targetEl.classList.remove('hidden');
    });
});
