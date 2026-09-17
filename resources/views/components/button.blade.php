@props(['variant' => 'primary', 'size' => 'md', 'href' => null])

@php
  $variants = [
      'primary'   => 'bg-ink text-paper hover:opacity-90',
      'secondary' => 'border border-rule text-ink hover:bg-paper-raised',
  ];

  $sizes = [
      'sm' => 'px-4 py-2 text-[14px]',      // Navigation — wie im Header der Startseite
      'md' => 'px-5 py-3 text-[15px]',      // Formular-CTA — wie der Hero-Button
  ];

  $base = 'inline-flex items-center justify-center gap-2 rounded-md font-medium no-underline transition-colors '
        . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->class($base) }}>{{ $slot }}</a>
@else
  <button {{ $attributes->merge(['type' => 'submit'])->class($base) }}>{{ $slot }}</button>
@endif
