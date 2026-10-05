@props(['href' => null, 'active' => false])

{{-- Row of the left page (week, azubi): the active row gets the stamp edge --}}
@php
    $classes = 'w-full flex items-center gap-3.5 px-5 py-3.5 text-left border-b border-rule cursor-pointer '
        . ($active ? 'bg-paper shadow-[inset_3px_0_0_var(--color-stamp)]' : 'bg-transparent hover:bg-paper');
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }}">{{ $slot }}</a>
@else
    <button type="button" class="{{ $classes }}">{{ $slot }}</button>
@endif
