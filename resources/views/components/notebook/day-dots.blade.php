@props(['filled' => 0, 'total' => 5])

<span class="flex gap-1">
    @for ($i = 0; $i < $total; $i++)
        <span
          @class([
            'w-[13px] h-[5px] rounded-sm',
            'bg-report' => $i < $filled,
            'bg-rule opacity-50' => $i >= $filled,
          ])
        ></span>
    @endfor
</span>
