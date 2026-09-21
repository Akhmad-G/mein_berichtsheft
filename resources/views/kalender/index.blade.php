<x-layouts.app title="Kalender">

  <x-heft.tabs current="kalender"
               :items="$tabs"
  />

  <x-heft>
    {{-- linke Seite: Monat --}}
    <x-slot:left>
      <x-heft.page-header :title="$monat->isoFormat('MMMM YYYY')"
                          tight
      >
        <x-slot:actions>
          <x-heft.stepper :prev="route('kalender', ['monat' => $monat->copy()->subMonth()->format('Y-m')])"
                          :next="route('kalender', ['monat' => $monat->copy()->addMonth()->format('Y-m')])"
                          :jump="route('kalender')"
                          jump-label="Heute"
          />
        </x-slot:actions>
      </x-heft.page-header>

      <x-kalender.month :days="$tage"
                        :selected="$tag->datum"
                        :today="today()"
      />
    </x-slot:left>

    {{-- rechte Seite: Tagesblatt --}}
    <x-slot:right>
      <x-kalender.week-strip :days="$wochentage"
                             :selected="$tag->datum"
                             :note="'KW ' . $tag->datum->isoWeek() . ' · ' . $wocheErfasst . '/5 Tage'"
      />

      <form method="POST"
            action="{{ route('tagesbericht.speichern', $tag) }}"
            class="px-[22px] py-5 flex flex-col gap-4 flex-1"
      >
        @csrf
        @method('PUT')

        <div class="flex flex-wrap items-baseline justify-between gap-3.5">
          <h2 class="font-display text-[21px]">{{ $tag->datum->isoFormat('dddd, DD.MM.YYYY') }}</h2>
          <x-stamp :status="$tag->statusStempel"
                   size="md"
          >{{ $tag->statusText }}</x-stamp>
        </div>

        <div class="flex flex-col gap-[7px]">
          <x-form.label for="taetigkeiten">Tätigkeiten</x-form.label>
          <textarea id="taetigkeiten"
                    name="taetigkeiten"
                    rows="8"
                    maxlength="600"
                    @readonly(! $darfSchreiben)placeholder="Ein Satz je Tätigkeit. Stichpunkte genügen."
                    class="w-full bg-paper-line border border-rule rounded-md px-3 py-2.5 text-[14.5px]
                               leading-relaxed resize-y focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink
                               read-only:text-ink-soft"
          >{{ old('taetigkeiten', $tag->taetigkeiten) }}</textarea>

          <div class="flex justify-between gap-3 text-[12px] text-ink-soft">
                        <span>{{ $darfSchreiben
                            ? 'Der Ausbilder liest den Wortlaut genau so.'
                            : 'Ansicht für Ausbilder — Einträge sind nicht bearbeitbar.' }}</span> <span>{{ mb_strlen($tag->taetigkeiten ?? '') }} / 600</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          <x-form.field name="dauer"
                        label="Dauer"
                        :value="$tag->dauer"
                        :readonly="! $darfSchreiben"
          />
          <x-form.field name="abteilung"
                        label="Abteilung"
                        :value="$tag->abteilung"
                        :readonly="! $darfSchreiben"
          />
        </div>

        <div class="border-t border-rule pt-[15px] flex flex-col gap-2.5">
          <div class="flex items-baseline justify-between gap-3">
            <span class="text-[12.5px] text-ink-soft">Lernschritte aus dem Ausbildungsplan</span>
            <span class="text-[12px] text-ink-soft">{{ $tag->lernschritte->count() }} zugeordnet</span>
          </div>

          <div class="flex flex-wrap gap-[7px]">
            @foreach ($lernschritte as $schritt)
              <x-kalender.plan-chip :schritt="$schritt"
                                    :checked="$tag->lernschritte->contains($schritt)"
                                    :disabled="! $darfSchreiben"
              />
            @endforeach
          </div>

          <p class="text-[12px] leading-snug text-ink-soft">
            Zugeordnete Schritte erscheinen später im Wochenbericht — eine eigene Planseite brauchst du dafür
            nicht. </p>
        </div>

        <div class="mt-auto border-t border-rule pt-[15px] flex flex-wrap items-center justify-between gap-3.5">
                    <span class="text-[12px] text-ink-soft">
                        @if ($tag->updated_at)
                        Zuletzt gespeichert {{ $tag->updated_at->isoFormat('HH:mm') }}
                      @endif
                    </span>

          @if ($darfSchreiben)
            <div class="flex gap-[9px]">
              <x-button variant="secondary"
                        size="sm"
                        :href="route('tagesbericht', ['datum' => $tag->datum->copy()->addDay()->toDateString()])"
              >
                Nächster Tag
              </x-button>
              <x-button size="sm">Speichern</x-button>
            </div>
          @endif
        </div>
      </form>
    </x-slot:right>
  </x-heft>

</x-layouts.app>
