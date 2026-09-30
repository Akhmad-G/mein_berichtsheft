@props(['prev' => '#', 'next' => '#', 'jump' => null, 'jumpLabel' => 'Heute'])

<div class="flex items-center gap-[7px]">
  <a href="{{ $prev }}"
     aria-label="Zurück"
     class="w-[27px] h-[27px] flex items-center justify-center border border-rule rounded-[5px] bg-paper hover:bg-paper-raised"
  >
    <svg viewBox="0 0 12 12"
         class="w-2.5 h-2.5"
    >
      <path d="M7.5 1.5 L3 6 L7.5 10.5"
            fill="none"
            stroke="currentColor"
            stroke-width="1.7"
            stroke-linecap="round"
            stroke-linejoin="round"
      />
    </svg>
  </a> <a href="{{ $next }}"
          aria-label="Weiter"
          class="w-[27px] h-[27px] flex items-center justify-center border border-rule rounded-[5px] bg-paper hover:bg-paper-raised"
  >
    <svg viewBox="0 0 12 12"
         class="w-2.5 h-2.5"
    >
      <path d="M4.5 1.5 L9 6 L4.5 10.5"
            fill="none"
            stroke="currentColor"
            stroke-width="1.7"
            stroke-linecap="round"
            stroke-linejoin="round"
      />
    </svg>
  </a>

  @if ($jump)
    <a href="{{ $jump }}"
       class="text-[12.5px] px-[11px] py-1.5 border border-rule rounded-[5px] bg-paper hover:bg-paper-raised whitespace-nowrap"
    >
      {{ $jumpLabel }}
    </a>
  @endif
</div>
