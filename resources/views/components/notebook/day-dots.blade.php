@props(['filled' => 0, 'total' => 5])

<span class="flex gap-1">
    @for ($i = 0; $i < $total; $i++)
        <span @class([
            'w-[13px] h-[5px] rounded-sm',
            $i < $filled ? 'bg-report' : 'bg-rule opacity-50',
        ])></span>
    @endfor
</span>
