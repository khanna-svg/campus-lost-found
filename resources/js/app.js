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
