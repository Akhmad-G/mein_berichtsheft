@props(['type' => 'text', 'invalid' => false])

{{-- Fokus: ink-Rand + 1px Ring (kein Tailwind-Blau); bei Fehler stamp --}}
<input type="{{ $type }}"
  {{ $attributes->class([
      'w-full bg-paper-line rounded-md px-3.5 py-2.5 text-[15px] text-ink',
      'placeholder:text-ink-soft/60 border transition-shadow',
      'focus:outline-none focus:ring-1',
      $invalid
          ? 'border-stamp focus:border-stamp focus:ring-stamp'
          : 'border-rule focus:border-ink focus:ring-ink',
  ]) }}
>
