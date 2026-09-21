@props(['current', 'items' => []])

<div class="flex relative z-1">
  @foreach ($items as $i => $item)
    @php
      $on = $current === $item['key'];
      $z  = $on ? 2 : count($items) - $i;
    @endphp

    <a href="{{ $item['href'] }}"
       style="z-index: {{ $z }}"
      @class([
          'relative flex items-center gap-[9px] border border-rule rounded-t-[9px] cursor-pointer',
          $i > 0 ? '-ml-2.5 pl-[29px] pr-5 py-[11px]' : 'px-5 py-[11px]',
          $on
              ? 'bg-paper-line text-ink border-b-paper-line -mb-px pb-3'
              : 'bg-[color-mix(in_oklab,var(--color-rule)_20%,var(--color-paper-raised))] text-ink-soft',
      ])
    >
            <span @class(['font-display text-[14px] tracking-[.02em]', $on && 'font-medium'])>
                {{ $item['label'] }}
            </span>

      @if (! empty($item['badge']))
        <span @class([
                    'text-[10.5px] tracking-[.06em] uppercase',
                    $on ? 'text-stamp' : 'text-ink-soft',
                ])>{{ $item['badge'] }}</span>
      @endif
    </a>
  @endforeach
</div>
