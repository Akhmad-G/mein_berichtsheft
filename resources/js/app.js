import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.querySelectorAll('[data-theme-toggle]').forEach(function (themeToggle) {
  themeToggle.addEventListener('click', function () {
    const isDark = document.documentElement.classList.toggle('dark');

    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.dispatchEvent(new CustomEvent('theme-changed'));
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

// ... existing code ...

document.querySelectorAll('[data-signature-modal]').forEach((wrap) => {
  const openButtons = wrap.querySelectorAll('[data-signature-open]');
  const closeButtons = wrap.querySelectorAll('[data-signature-close]');
  const backdrop = wrap.querySelector('[data-signature-backdrop]');
  const canvas = wrap.querySelector('[data-signature-pad]');
  const clearButton = wrap.querySelector('[data-signature-clear]');
  const submitButton = wrap.querySelector('[data-signature-submit]');
  const input = wrap.querySelector('[data-signature-input]');

  if (!backdrop || !canvas || !clearButton || !submitButton || !input) return;

  const ctx = canvas.getContext('2d');
  let drawing = false;
  let hasDrawing = false;
  let paths = [];
  let currentPath = '';

  const applyInkColor = () => {
    ctx.strokeStyle = document.documentElement.classList.contains('dark')
      ? '#EDE7D8'
      : '#20262C';
  };

  applyInkColor();
  document.addEventListener('theme-changed', applyInkColor);
  ctx.lineWidth = 3;
  ctx.lineCap = 'round';
  ctx.lineJoin = 'round';

  const setOpen = (open) => {
    backdrop.hidden = !open;
    document.body.classList.toggle('overflow-hidden', open);

    if (!open) {
      clearSignature();
    }
  };

  const getPos = (event) => {
    const rect = canvas.getBoundingClientRect();
    const point = event.touches ? event.touches[0] : event;

    return {
      x: (point.clientX - rect.left) * (canvas.width / rect.width),
      y: (point.clientY - rect.top) * (canvas.height / rect.height),
    };
  };

  const start = (event) => {
    drawing = true;
    hasDrawing = true;
    submitButton.disabled = false;

    const pos = getPos(event);
    currentPath = `M${Math.round(pos.x)} ${Math.round(pos.y)}`;
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);

    event.preventDefault();
  };

  const move = (event) => {
    if (!drawing) return;

    const pos = getPos(event);
    currentPath += ` L${Math.round(pos.x)} ${Math.round(pos.y)}`;
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();

    event.preventDefault();
  };

  const stop = () => {
    if (drawing && currentPath) {
      paths.push(currentPath);
      currentPath = '';
    }

    drawing = false;
    input.value = hasDrawing
      ? JSON.stringify({viewBox: '0 0 900 260', paths})
      : '';
  };

  openButtons.forEach((button) => button.addEventListener('click', () => setOpen(true)));
  closeButtons.forEach((button) => button.addEventListener('click', () => setOpen(false)));

  backdrop.addEventListener('click', (event) => {
    if (event.target === backdrop) setOpen(false);
  });

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  canvas.addEventListener('mouseup', stop);
  canvas.addEventListener('mouseleave', stop);
  canvas.addEventListener('touchstart', start);
  canvas.addEventListener('touchmove', move);
  canvas.addEventListener('touchend', stop);

  const clearSignature = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    paths = [];
    currentPath = '';
    hasDrawing = false;
    input.value = '';
    submitButton.disabled = true;
  };

  clearButton.addEventListener('click', clearSignature);

  wrap.querySelector('form')?.addEventListener('submit', () => {
    input.value = JSON.stringify({viewBox: '0 0 900 260', paths});
  });
});

Alpine.start();
