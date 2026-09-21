@props(['rolle', 'name' => null, 'datum' => null])

<div class="flex flex-col gap-2">
    <span @class([
        'font-display text-[15px] min-h-[22px]',
        $datum ? 'text-bericht' : 'text-ink-soft italic',
    ])>
        {{ $datum ? $name . ' · ' . $datum->format('d.m.Y') : 'offen' }}
    </span>

    <span class="h-px bg-rule"></span>
    <span class="text-[11.5px] text-ink-soft">{{ $rolle }}</span>
</div>
