@props(['role', 'name' => null, 'date' => null])

<div class="flex flex-col gap-2">
    <span @class([
        'font-display text-[15px] min-h-[22px]',
        $date ? 'text-report' : 'text-ink-soft italic',
    ])>
        {{ $date ? $name . ' · ' . $date->format('d.m.Y') : 'offen' }}
    </span>

    <span class="h-px bg-rule"></span>
    <span class="text-[11.5px] text-ink-soft">{{ $role }}</span>
</div>
