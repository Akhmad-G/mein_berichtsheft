@props(['type' => 'text', 'invalid' => false])

<input
    type="{{ $type }}"
    {{ $attributes->class([
        'w-full bg-paper-line border rounded-md px-3.5 py-2.5 text-[15px] text-ink placeholder:text-ink-soft/60',
        'focus:outline-none focus:border-ink',
        $invalid ? 'border-stamp' : 'border-rule',
    ]) }}
>
