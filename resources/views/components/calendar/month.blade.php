@props(['cells' => [], 'selected' => null, 'today' => null])

{{--
  $cells: [['date' => CarbonImmutable, 'inMonth' => bool,
            'state' => report|open|school|vacation|sick|holiday|free], …]  — Monday-first grid
  The soft tint comes from color-mix, so the same class works in both themes.
--}}
@php
  $tint = [
      'report'   => 'bg-[color-mix(in_oklab,var(--color-report-soft)_75%,var(--color-paper))]',
      'vacation' => 'bg-[color-mix(in_oklab,var(--color-vacation-soft)_75%,var(--color-paper))]',
      'sick'     => 'bg-[color-mix(in_oklab,var(--color-sick-soft)_75%,var(--color-paper))]',
      'school'   => 'bg-[color-mix(in_oklab,var(--color-school-soft)_75%,var(--color-paper))]',
      'holiday'  => 'bg-[color-mix(in_oklab,var(--color-holiday-soft)_75%,var(--color-paper))]',
      'open'     => 'bg-transparent',
      'free'     => 'bg-transparent',
  ];
  $labels = [
      'vacation' => 'Urlaub', 'sick' => 'Krank',
      'school'   => 'Schule', 'holiday' => 'Feiertag',
  ];
@endphp

<div class="flex flex-col flex-1">
  <div class="grid grid-cols-7 border-b border-rule">
    @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $weekday)
      <div class="px-[9px] py-2 text-[10.5px] tracking-[.08em] uppercase text-ink-soft">{{ $weekday }}</div>
    @endforeach
  </div>

  <div class="grid grid-cols-7 flex-1">
    @foreach ($cells as $cell)
      @php
        $state      = $cell['state'];
        $inMonth    = $cell['inMonth'];
        $date       = $cell['date'];
        $isSelected = $inMonth && $selected && $date->isSameDay($selected);
        $isToday    = $inMonth && $today && $date->isSameDay($today);
        $clickable  = $inMonth && $state !== 'free';
      @endphp

      <a @if ($clickable) href="{{ route('calendar', ['day' => $date->toDateString()]) }}" @endif
        @class([
            'flex flex-col justify-between gap-1 min-h-[66px] px-[9px] py-2 text-left',
            'border-r border-b border-rule',
            $inMonth ? $tint[$state] : 'bg-transparent',
            'pointer-events-none cursor-default' => ! $clickable,
            'shadow-[inset_0_0_0_2px_var(--color-ink)]' => $isSelected,
        ])
      >
        <span class="flex items-start justify-between gap-1">
            <span
              @class([
                'font-display text-[15px]',
                'text-sick' => $inMonth && $date->isWeekend(),
                'text-ink-soft opacity-45' => ! $inMonth,
                'border-b-2 border-stamp pb-px' => $isToday,
              ])
            >{{ $date->day }}</span>

            @if ($inMonth && $state === 'report')
            <x-calendar.ink-check />
          @endif
        </span>

        @if ($inMonth && isset($labels[$state]))
          <span class="text-[10.5px] text-ink-soft truncate">{{ $labels[$state] }}</span>
        @endif
      </a>
    @endforeach
  </div>

  <x-calendar.legend />
</div>
