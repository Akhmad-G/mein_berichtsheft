import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.querySelectorAll('[data-theme-toggle]').forEach(function (themeToggle) {
  themeToggle.addEventListener('click', function () {
    const isDark = document.documentElement.classList.toggle('dark');

    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
});

document.querySelectorAll('[data-user-menu]').forEach((wrap) => {
  const button = wrap.querySelector('[data-user-menu-button]');
  const panel = wrap.querySelector('[data-user-menu-panel]');
  const chevron = wrap.querySelector('[data-user-menu-chevron]');

  const setOpen = (open) => {
    panel.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
    chevron.classList.toggle('rotate-180', open);
  };

  button.addEventListener('click', (e) => {
    e.stopPropagation();
    setOpen(panel.hidden);
  });

  document.addEventListener('click', (e) => {
    if (!wrap.contains(e.target)) setOpen(false);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') setOpen(false);
  });
});

Alpine.start();
