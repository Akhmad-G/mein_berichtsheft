@props(['step' => null, 'title'])

<div class="flex flex-col gap-3.5">
    <div class="flex items-baseline gap-2.5 border-b border-rule pb-2">
        @if ($step)
            <span class="font-display text-[14px] text-stamp">{{ $step }}</span>
        @endif
        <h2 class="font-display text-[18px]">{{ $title }}</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        {{ $slot }}
    </div>

    @isset($note)
        <p class="text-[12.5px] text-ink-soft">{{ $note }}</p>
    @endisset
</div>
