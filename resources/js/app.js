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
