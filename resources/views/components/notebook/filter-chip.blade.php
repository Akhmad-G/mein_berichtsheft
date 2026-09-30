@props(['href' => '#', 'active' => false])

<a href="{{ $href }}" @class([
    'text-[12.5px] px-3 py-1.5 rounded-full border border-rule',
    $active ? 'bg-paper-raised text-ink' : 'bg-transparent text-ink-soft hover:bg-paper',
])>{{ $slot }}</a>
