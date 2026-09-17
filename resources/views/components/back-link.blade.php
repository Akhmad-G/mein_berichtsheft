@props(['href' => null, 'label' => 'Zur Startseite'])

<a href="{{ $href ?? url('/') }}"
   class="flex items-center gap-1.5 text-[13px] text-ink-soft no-underline hover:text-ink"
>
  <svg viewBox="0 0 12 12"
       class="w-[11px] h-[11px]"
  >
    <path d="M7.5 2 L3.5 6 L7.5 10"
          fill="none"
          stroke="currentColor"
          stroke-width="1.6"
          stroke-linecap="round"
          stroke-linejoin="round"
    />
  </svg>
  {{ $label }}
</a>
