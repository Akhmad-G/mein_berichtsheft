@props(['week' => 24, 'range' => '09.–13. Juni', 'filled' => 4])

<div class="border-t border-rule pt-4">
    <div class="flex justify-between items-baseline mb-2.5">
        <span class="font-display text-[15px]">Woche {{ $week }}</span>
        <span class="text-[12.5px] text-ink-soft">{{ $range }}</span>
    </div>

    @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr'] as $i => $tag)
        <div class="flex items-center gap-3 py-1.5 border-t border-rule">
            <span class="w-[26px] text-[13px] text-ink-soft shrink-0">{{ $tag }}</span>
            <span class="flex-1 h-px bg-rule"></span>

            @if ($i < $filled)
                <x-report-check class="mt-0 animate-pop-in" style="animation-delay: {{ $i * 140 }}ms" />
            @else
                <span class="w-[17px] h-[17px] rounded-full border border-dashed border-rule shrink-0"></span>
            @endif
        </div>
    @endforeach
</div>
