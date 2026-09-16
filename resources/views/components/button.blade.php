@props(['variant' => 'primary', 'href' => null])

@php
    $classes = [
        'primary'   => 'bg-ink text-paper hover:bg-ink/90',
        'secondary' => 'border border-rule text-ink hover:bg-paper-raised',
    ][$variant];

    $base = "inline-flex items-center justify-center gap-2 rounded-md px-5 py-3 text-[15px] font-medium transition-colors {$classes}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($base) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($base) }}>{{ $slot }}</button>
@endif
