<div class="border-t border-rule px-5 py-3 flex flex-wrap gap-x-4 gap-y-[7px]">
  @foreach ([
      'Bericht geschrieben' => 'bericht-soft',
      'Urlaub'              => 'urlaub-soft',
      'Krank'               => 'krank-soft',
      'Berufsschule'        => 'schule-soft',
      'Feiertag'            => 'fest-soft',
  ] as $label => $token)
    <span class="flex items-center gap-[7px] text-[12px] text-ink-soft">
            <span class="w-3 h-3 rounded-[3px] border border-rule shrink-0
                         bg-[color-mix(in_oklab,var(--color-{{ $token }})_75%,var(--color-paper))]"
            ></span>
            {{ $label }}
        </span>
  @endforeach
</div>
