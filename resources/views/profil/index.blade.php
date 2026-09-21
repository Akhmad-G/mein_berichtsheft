<x-layouts.app title="Profil">

  {{-- ohne Register: Rückweg als eigener Knopf, Mappe rundum gerundet --}}
  <div class="flex pb-2.5">
    <x-button variant="secondary"
              size="sm"
              :href="route('start')"
    >
      <svg viewBox="0 0 12 12"
           class="w-2.5 h-2.5"
      >
        <path d="M7.5 1.5 L3 6 L7.5 10.5"
              fill="none"
              stroke="currentColor"
              stroke-width="1.7"
              stroke-linecap="round"
              stroke-linejoin="round"
        />
      </svg>
      Zurück zum Heft
    </x-button>
  </div>

  <x-heft :tabs="false">
    {{-- linke Seite: Stammdaten --}}
    <x-slot:left>
      <x-heft.page-header :title="auth()->user()->istAusbilder() ? 'Mein Zugang' : 'Stammdaten'"
                          tight
      />

      <div class="px-5 pt-1.5 pb-5 flex flex-col flex-1">
        @foreach ($stammdaten as $label => $wert)
          <div class="flex items-baseline justify-between gap-4 py-[11px] border-b border-rule">
            <span class="text-[12.5px] text-ink-soft shrink-0">{{ $label }}</span>
            <span class="text-[14px] text-right">{{ $wert }}</span>
          </div>
        @endforeach

        <div class="mt-[18px] flex flex-wrap gap-[9px]">
          <x-button variant="secondary"
                    size="sm"
                    :href="route('profil.bearbeiten')"
          >Daten ändern
          </x-button>
          <x-button variant="secondary"
                    size="sm"
                    :href="route('password.edit')"
          >Passwort ändern
          </x-button>
        </div>
      </div>
    </x-slot:left>

    {{-- rechte Seite: Deckblatt des Nachweises --}}
    <x-slot:right>
      <x-heft.page-header>
        <x-slot:lead>
          @if (auth()->user()->istAusbilder())
            {{-- Nachweis direkt hier wechseln, ohne das Profil zu verlassen --}}
            <form method="GET"
                  action="{{ route('profil') }}"
                  class="flex items-center gap-[7px] min-w-0"
            >
              <span class="text-[12px] tracking-[.06em] uppercase text-ink-soft whitespace-nowrap">Nachweis</span>
              <select name="azubi"
                      onchange="this.form.submit()"
                      class="text-[13.5px] px-2.5 py-[7px] border border-rule rounded-md bg-paper
                                           min-w-0 cursor-pointer focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink"
              >
                @foreach ($azubis as $a)
                  <option value="{{ $a->id }}" @selected($azubi->is($a))>{{ $a->vollerName }}</option>
                @endforeach
              </select>
            </form>
          @else
            <h2 class="font-display text-[18px] min-w-0 truncate">Deckblatt</h2>
          @endif
        </x-slot:lead>

        <x-slot:actions>
          <x-button variant="secondary"
                    size="sm"
                    :href="route('nachweis.drucken', $azubi)"
          >Drucken
          </x-button>
          <x-button variant="secondary"
                    size="sm"
                    :href="route('nachweis.pdf', $azubi)"
          >Gesamt-PDF
          </x-button>
        </x-slot:actions>
      </x-heft.page-header>

      <div class="px-[22px] py-[22px] flex flex-col gap-4 flex-1">
        <div class="border border-rule rounded-lg bg-paper-line px-6 py-[26px] flex flex-col gap-5 flex-1">
          <div>
            <span class="text-[11.5px] tracking-[.1em] uppercase text-ink-soft">Ausbildungsnachweis</span>
            <h3 class="font-display text-[24px] mt-2 leading-tight">{{ $azubi->vollerName }}</h3>
          </div>

          <div class="flex flex-col">
            @foreach ([
                'Ausbildungsberuf' => $azubi->ausbildungsberuf,
                'Betrieb' => $azubi->ausbildungsbetrieb,
                'Abteilung' => $azubi->abteilung,
                'Zeitraum' => $azubi->ausbildungszeitraum,
                'Ausbilder' => $azubi->ausbilder?->vollerName,
            ] as $label => $wert)
              <div class="flex items-baseline justify-between gap-3.5 py-2.5 border-t border-rule">
                <span class="text-[12.5px] text-ink-soft">{{ $label }}</span>
                <span class="text-[13.5px]">{{ $wert }}</span>
              </div>
            @endforeach
          </div>

          <div class="mt-auto flex items-end justify-between gap-4">
                        <span class="text-[12px] leading-snug text-ink-soft max-w-[230px]">
                            Dieses Blatt steht vor jedem Ausdruck des Nachweises.
                        </span>
            <x-stamp status="signiert"
                     size="md"
            >
              {{ $azubi->signierteWochen }} Wochen unterschrieben
            </x-stamp>
          </div>
        </div>
      </div>
    </x-slot:right>
  </x-heft>

</x-layouts.app>
