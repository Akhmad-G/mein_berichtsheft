{{-- classes written out in full so Tailwind finds them --}}
<div class="border-t border-rule px-5 py-3 flex flex-wrap gap-x-4 gap-y-[7px]">
  @foreach ([
      'Bericht geschrieben' => 'bg-[color-mix(in_oklab,var(--color-report-soft)_75%,var(--color-paper))]',
      'Urlaub'              => 'bg-[color-mix(in_oklab,var(--color-vacation-soft)_75%,var(--color-paper))]',
      'Krank'               => 'bg-[color-mix(in_oklab,var(--color-sick-soft)_75%,var(--color-paper))]',
      'Berufsschule'        => 'bg-[color-mix(in_oklab,var(--color-school-soft)_75%,var(--color-paper))]',
      'Feiertag'            => 'bg-[color-mix(in_oklab,var(--color-holiday-soft)_75%,var(--color-paper))]',
  ] as $label => $tint)
    <span class="flex items-center gap-[7px] text-[12px] text-ink-soft">
            <span class="w-3 h-3 rounded-[3px] border border-rule shrink-0 {{ $tint }}"></span>
            {{ $label }}
        </span>
  @endforeach
</div>
