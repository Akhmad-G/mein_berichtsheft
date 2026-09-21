<x-layouts.app title="Meine Azubis">

  <x-heft.tabs current="azubis"
               :items="$tabs"
  />

  <x-heft>
    <x-slot:left>
      <x-heft.page-header title="Meine Azubis"
                          tight
      >
        <x-slot:actions>
          <x-button variant="secondary"
                    size="sm"
                    :href="route('azubis', ['sort' => 'offen'])"
          >
            Offene zuerst
          </x-button>
        </x-slot:actions>
      </x-heft.page-header>

      <div class="flex-1">
        @foreach ($azubis as $a)
          <x-heft.ledger-row :href="route('azubis.zeigen', $a)"
                             :active="$azubi->is($a)"
          >
            <span class="w-[30px] h-[30px] shrink-0 flex items-center justify-center rounded-md
                         border border-rule bg-paper-raised text-[11.5px]"
            >{{ $a->initialen }}</span>

            <span class="flex-1 min-w-0 flex flex-col items-start gap-1">
              <span class="text-[14px]">{{ $a->vollerName }}</span>
              <span class="text-[12px] text-ink-soft truncate">{{ $a->ausbildungsberuf }} · {{ $a->abteilung }}</span>
            </span>

            <x-stamp :status="$a->offeneWochen ? 'wartet' : 'signiert'">
              {{ $a->offeneWochen ? $a->offeneWochen . ' offen' : 'aktuell' }}
            </x-stamp>
          </x-heft.ledger-row>
        @endforeach
      </div>
    </x-slot:left>

    <x-slot:right>
      <x-heft.page-header :title="$azubi->vollerName">
        <x-slot:actions>
          <x-stamp :status="$azubi->offeneWochen ? 'wartet' : 'signiert'"
                   size="md"
          >
            {{ $azubi->offeneWochen ? $azubi->offeneWochen . ' Wochen offen' : 'alles unterschrieben' }}
          </x-stamp>
        </x-slot:actions>
      </x-heft.page-header>

      <div class="px-[22px] py-[22px] flex flex-col gap-[18px] flex-1">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          @foreach ([
              'Unterschrieben' => $azubi->signierteWochen . ' Wochen',
              'Wartet auf dich' => $azubi->offeneWochen . ' Wochen',
              'Erfasste Tage' => $azubi->erfassteTage,
          ] as $label => $wert)
            <div class="border border-rule rounded-lg bg-paper-line px-[15px] py-3.5 flex flex-col gap-[5px]">
              <span class="text-[12px] text-ink-soft">{{ $label }}</span>
              <span class="font-display text-[19px]">{{ $wert }}</span>
            </div>
          @endforeach
        </div>

        <div class="flex flex-col">
          <span class="text-[12.5px] text-ink-soft pb-2">Zuletzt eingereicht</span>

          @foreach ($azubi->letzteWochen as $w)
            <div class="grid grid-cols-[52px_minmax(0,1fr)_auto_auto] gap-3.5 items-center py-3 border-t border-rule">
              <span class="font-display text-[16px]">KW {{ $w->kw }}</span>
              <span class="min-w-0 text-[13px] text-ink-soft truncate">{{ $w->zeitraum }}</span>
              <x-stamp :status="$w->statusStempel">{{ $w->statusText }}</x-stamp>
              <x-button variant="secondary"
                        size="sm"
                        :href="route('wochenbericht', $w)"
              >Öffnen
              </x-button>
            </div>
          @endforeach
        </div>

        <p class="mt-auto text-[12px] leading-snug text-ink-soft">
          Den Kalender des Azubi brauchst du nicht — Wochen wählst du hier oder im Register „Wochenberichte“. </p>
      </div>
    </x-slot:right>
  </x-heft>

</x-layouts.app>
