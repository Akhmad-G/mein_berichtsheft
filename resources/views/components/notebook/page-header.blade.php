@props(['title' => null, 'tight' => false])

{{--
  Header of both pages: min-h-16 with centred content — so the dividing
  lines of the left and right page sit at the same height.
  Content stays on ONE line (nowrap), otherwise the box grows and the fold breaks.
--}}
<div @class([
    'min-h-16 box-border border-b border-rule flex items-center justify-between gap-3 flex-nowrap',
    $tight ? 'px-5 py-2.5' : 'px-[22px] py-2.5',
])>
  @if ($title)
    <h2 class="font-display text-[17px] min-w-0 truncate">{{ $title }}</h2>
  @else
    {{ $lead ?? '' }}
  @endif

  @isset($actions)
    <div class="flex items-center gap-2 shrink-0">
      {{ $actions }}
    </div>
  @endisset
</div>
