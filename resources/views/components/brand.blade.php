@props(['size' => 26, 'text' => 16])

<a href="{{ url('/') }}"
   class="flex items-center gap-2.5 no-underline text-ink"
>
  <svg viewBox="0 0 120 120"
       style="width: {{ $size }}px; height: {{ $size }}px"
       xmlns="http://www.w3.org/2000/svg"
  >
    <rect x="10"
          y="38"
          width="16"
          height="44"
          rx="6"
          class="fill-ink"
    />
    <rect x="30"
          y="38"
          width="16"
          height="44"
          rx="6"
          class="fill-ink"
    />
    <rect x="50"
          y="38"
          width="16"
          height="44"
          rx="6"
          class="fill-ink"
    />
    <rect x="70"
          y="38"
          width="16"
          height="44"
          rx="6"
          class="fill-ink"
    />
    <rect x="90"
          y="38"
          width="16"
          height="44"
          rx="6"
          class="fill-stamp"
    />
    <path d="M93.5 60 L97 64.5 L103 51"
          fill="none"
          class="stroke-paper"
          stroke-width="4"
          stroke-linecap="round"
          stroke-linejoin="round"
    />
  </svg>
  <span class="font-display font-medium"
        style="font-size: {{ $text }}px"
  >Mein Tagesbericht</span> </a>
