@props(['step' => null, 'title'])

<div class="flex flex-col gap-3">
    <div class="flex items-baseline gap-2.5 border-b border-rule pb-2">
        @if ($step)
            <span class="font-display text-[14px] text-stamp">{{ $step }}</span>
        @endif
        <h2 class="font-display text-[18px]">{{ $title }}</h2>
    </div>

    {{-- gap-y klein halten: die Fehlerzeile der Felder trägt den Abstand --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3.5 gap-y-1">
        {{ $slot }}
    </div>
</div>
