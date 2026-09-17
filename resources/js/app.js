import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.querySelectorAll('[data-theme-toggle]').forEach(function (themeToggle) {
  themeToggle.addEventListener('click', function () {
    const isDark = document.documentElement.classList.toggle('dark');

    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
});

Alpine.start();
