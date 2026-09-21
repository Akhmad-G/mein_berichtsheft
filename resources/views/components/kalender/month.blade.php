@props(['days' => [], 'selected' => null, 'today' => null])

@php
  $tint = [
      'bericht'  => 'bg-[color-mix(in_oklab,var(--color-bericht-soft)_75%,var(--color-paper))]',
      'urlaub'   => 'bg-[color-mix(in_oklab,var(--color-urlaub-soft)_75%,var(--color-paper))]',
      'krank'    => 'bg-[color-mix(in_oklab,var(--color-krank-soft)_75%,var(--color-paper))]',
      'schule'   => 'bg-[color-mix(in_oklab,var(--color-schule-soft)_75%,var(--color-paper))]',
      'feiertag' => 'bg-[color-mix(in_oklab,var(--color-fest-soft)_75%,var(--color-paper))]',
      'offen'    => 'bg-transparent',
      'frei'     => 'bg-transparent',
  ];
  $tag = [
      'urlaub' => 'Urlaub', 'krank' => 'Krank',
      'schule' => 'Schule', 'feiertag' => 'Feiertag',
  ];
@endphp

<div class="flex flex-col flex-1">
  <div class="grid grid-cols-7 border-b border-rule">
    @foreach (['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $wd)
      <div class="px-[9px] py-2 text-[10.5px] tracking-[.08em] uppercase text-ink-soft">{{ $wd }}</div>
    @endforeach
  </div>

  <div class="grid grid-cols-7 flex-1">
    @foreach ($days as $day)
      @php
        $art = $day['art'];
        $in  = $day['imMonat'];
        $d   = $day['datum'];
        $isSelected = $in && $selected && $d->isSameDay($selected);
        $isToday    = $in && $today && $d->isSameDay($today);
      @endphp

      <a href="{{ $in ? route('tagesbericht', ['datum' => $d->toDateString()]) : '#' }}"
        @class([
            'flex flex-col justify-between gap-1 min-h-[66px] px-[9px] py-2 text-left',
            'border-r border-b border-rule',
            $in ? $tint[$art] : 'bg-transparent pointer-events-none',
            $isSelected && 'shadow-[inset_0_0_0_2px_var(--color-ink)]',
        ])
      >
                <span class="flex items-start justify-between gap-1">
                    <span @class([
                        'font-display text-[15px]',
                        ! $in && 'text-ink-soft opacity-45',
                        $isToday && 'border-b-2 border-stamp pb-px',
                    ])>{{ $d->day }}</span>

                    @if ($art === 'bericht')
                    <x-kalender.ink-check />
                  @endif
                </span>

        @if ($in && isset($tag[$art]))
          <span class="text-[10.5px] text-ink-soft truncate">{{ $tag[$art] }}</span>
        @endif
      </a>
    @endforeach
  </div>

  <x-kalender.legende />
</div>
