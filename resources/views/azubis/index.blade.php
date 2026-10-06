<x-layouts.app title="Meine Azubis">

  <x-notebook.tabs current="azubis"
                   :items="$tabs"
  />

  <x-notebook>
    <x-slot:left>
      <x-notebook.page-header title="Meine Azubis" tight>
        <x-slot:actions>
          <x-button variant="secondary"
                    size="sm"
                    :href="route('azubis.index', ['sort' => 'pending'])"
          >
            Offene zuerst
          </x-button>
        </x-slot:actions>
      </x-notebook.page-header>

      <div class="flex-1">
        @forelse ($rows as $row)
          <x-notebook.ledger-row :href="route('azubis.show', $row->user)"
                                 :active="$selected?->user->is($row->user)"
          >
            <span class="w-[30px] h-[30px] shrink-0 flex items-center justify-center rounded-md
                         border border-rule bg-paper-raised text-[11.5px]"
            >{{ $row->user->initials }}</span>

            <span class="flex-1 min-w-0 flex flex-col items-start gap-1">
              <span class="text-[14px]">{{ $row->user->name }}</span>
              <span class="text-[12px] text-ink-soft truncate">
                {{ collect([$row->user->ausbildungsberuf, $row->user->abteilung])->filter()->join(' · ') }}
              </span>
            </span>

            <x-stamp :status="$row->pending ? 'pending' : 'signed'">
              {{ $row->pending ? $row->pending . ' offen' : 'aktuell' }}
            </x-stamp>
          </x-notebook.ledger-row>
        @empty
          <p class="px-5 py-6 text-[13px] text-ink-soft">Dir sind noch keine Azubis zugeordnet.</p>
        @endforelse
      </div>
    </x-slot:left>

    <x-slot:right>
      @if ($selected)
        <x-notebook.page-header :title="$selected->user->name">
          <x-slot:actions>
            <x-stamp :status="$selected->pending ? 'pending' : 'signed'"
                     size="md"
            >
              {{ $selected->pending ? $selected->pending . ' Wochen offen' : 'alles unterschrieben' }}
            </x-stamp>
          </x-slot:actions>
        </x-notebook.page-header>

        <div class="px-[22px] py-[22px] flex flex-col gap-[18px] flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            @foreach ([
                'Unterschrieben ' . $year => $selected->signed . ' Wochen',
                'Wartet auf dich'         => $selected->pending . ' Wochen',
                'Erfasste Tage ' . $year  => $selected->recordedDays,
            ] as $label => $value)
              <div class="border border-rule rounded-lg bg-paper-line px-[15px] py-3.5 flex flex-col gap-[5px]">
                <span class="text-[12px] text-ink-soft">{{ $label }}</span>
                <span class="font-display text-[19px]">{{ $value }}</span>
              </div>
            @endforeach
          </div>

          <div class="flex flex-col">
            <span class="text-[12.5px] text-ink-soft pb-2">Zuletzt eingereicht</span>

            @forelse ($recentWeeks as $w)
              <div class="grid grid-cols-[52px_minmax(0,1fr)_auto_auto] gap-3.5 items-center py-3 border-t border-rule">
                <span class="font-display text-[16px]">KW {{ $w->week }}</span>
                <span class="min-w-0 text-[13px] text-ink-soft truncate">{{ $w->period() }}</span>
                <x-stamp :status="$w->status->stamp()">{{ $w->status->label() }}</x-stamp>
                <x-button variant="secondary"
                          size="sm"
                          :href="route('weekly-reports.show', $w->routeParams())"
                >Öffnen
                </x-button>
              </div>
            @empty
              <p class="py-3 border-t border-rule text-[13px] text-ink-soft">Noch nichts eingereicht.</p>
            @endforelse
          </div>

          <p class="mt-auto text-[12px] leading-snug text-ink-soft">
            Den Kalender des Azubi brauchst du nicht — Wochen wählst du hier oder im Register „Wochenberichte“. </p>
        </div>
      @else
        <x-notebook.page-header title="Kein Azubi ausgewählt" />
      @endif
    </x-slot:right>
  </x-notebook>

</x-layouts.app>
