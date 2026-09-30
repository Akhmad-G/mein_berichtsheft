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

// Day type on the day sheet (x-calendar.day-type).
// Chip styling comes from has-checked: — here we only toggle blocks and texts.
document.querySelectorAll('[data-day-type]').forEach((group) => {
  const form = group.closest('form');
  if (!form) return;

  const work      = form.querySelector('[data-day-type-work]');
  const absence   = form.querySelector('[data-day-type-absence]');
  const title     = form.querySelector('[data-day-type-title]');
  const submit    = form.querySelector('[data-day-type-submit]');
  const noteLabel = form.querySelector('[data-day-type-note-label]');
  const note      = form.querySelector('[data-day-type-note]');
  const from      = form.querySelector('[data-day-type-from]');
  const to        = form.querySelector('[data-day-type-to]');

  const apply = () => {
    const checked = group.querySelector('input[name="type"]:checked');
    if (!checked) return;
    const type = checked.value;
    const label = checked.closest('[data-day-type-chip]').dataset.label;
    const isWork = type === 'work';
    const isSick = type === 'sick';

    if (work) work.hidden = !isWork;
    if (absence) absence.hidden = isWork;
    if (title) title.textContent = label;
    if (submit) submit.textContent = isWork ? 'Speichern' : `${label} speichern`;
    if (noteLabel) noteLabel.textContent = isSick ? 'Nachweis / Bemerkung' : 'Bemerkung';
    if (note) note.placeholder = isSick ? 'z. B. AU bis 04.09. eingereicht' : 'optional';
  };

  group.addEventListener('change', apply);

  // "Bis" never before "Von"
  if (from && to) {
    to.min = from.value;
    from.addEventListener('change', () => {
      if (to.value < from.value) to.value = from.value;
      to.min = from.value;
    });
  }

  apply();
});

Alpine.start();
