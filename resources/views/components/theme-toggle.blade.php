{{-- gleicher Schalter wie auf der Startseite --}}
<button type="button"
        data-theme-toggle
        aria-label="Theme wechseln"
  {{ $attributes->class('p-1.5 leading-none border border-rule rounded-md hover:bg-paper-raised transition-colors') }}
>
  <svg viewBox="0 0 24 24"
       class="w-4 h-4 block stroke-current dark:hidden"
       fill="none"
       stroke-width="1.8"
       stroke-linecap="round"
       stroke-linejoin="round"
  >
    <path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a6.8 6.8 0 0 0 10.5 10.5Z" />
  </svg>
  <svg viewBox="0 0 24 24"
       class="w-4 h-4 hidden dark:block stroke-current"
       fill="none"
       stroke-width="1.8"
       stroke-linecap="round"
       stroke-linejoin="round"
  >
    <circle cx="12"
            cy="12"
            r="4.5"
    />
    <path d="M12 2.5v2.5M12 19v2.5M4.6 4.6l1.8 1.8M17.6 17.6l1.8 1.8M2.5 12H5M19 12h2.5M4.6 19.4l1.8-1.8M17.6 6.4l1.8-1.8" />
  </svg>
</button>
