<x-layouts.app title="Kalender">

  <x-heft.tabs current="kalender"
               :items="$tabs"
  />

  <x-heft>
    {{-- linke Seite: Monat --}}
    <x-slot:left>
      <x-heft.page-header :title="$month->isoFormat('MMMM YYYY')"
                          tight
      >
        <x-slot:actions>
          <x-heft.stepper :prev="route('kalender', ['month' => $month->copy()->subMonth()->format('Y-m')])"
                          :next="route('kalender', ['month' => $month->copy()->addMonth()->format('Y-m')])"
                          :jump="route('kalender')"
                          jump-label="Heute"
          />
        </x-slot:actions>
      </x-heft.page-header>

      <x-kalender.month :days="$days"
                        :selected="$day->date"
                        :today="today()"
      />
    </x-slot:left>

    {{-- rechte Seite: Tagesblatt --}}
    <x-slot:right>
      <x-kalender.week-strip :days="$weekdays"
                             :selected="$day->date"
                             :note="'KW ' . $day->date->isoWeek() . ' · ' . $recordedWeekdays . '/5 Tage'"
      />

      <form method="POST"
            action="{{ route('tagesbericht.speichern', ['date' => $day->date->toDateString()]) }}"
            class="px-[22px] py-5 flex flex-col gap-4 flex-1"
      >
        @csrf
        @method('PUT')

        <div class="flex flex-wrap items-baseline justify-between gap-3.5">
          <h2 class="font-display text-[21px]">{{ $day->date->isoFormat('dddd, DD.MM.YYYY') }}</h2>
          <x-stamp :status="$day->statusStempel"
                   size="md"
          >{{ $day->statusText }}</x-stamp>
        </div>

        <div class="flex flex-col gap-[7px]">
          <x-form.label for="taetigkeiten">Tätigkeiten</x-form.label>
          <textarea id="taetigkeiten"
                    name="taetigkeiten"
                    rows="8"
                    maxlength="600"
                    @readonly(!$canWrite)
                    placeholder="Ein Satz je Tätigkeit. Stichpunkte genügen."
                    class="w-full bg-paper-line border border-rule rounded-md px-3 py-2.5 text-[14.5px]
                               leading-relaxed resize-y focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink
                               read-only:text-ink-soft"
          >{{ old('taetigkeiten', $day->taetigkeiten) }}</textarea>

          <div class="flex justify-between gap-3 text-[12px] text-ink-soft">
            <span>{{ $canWrite
                ? 'Der Ausbilder liest den Wortlaut genau so.'
                : 'Ansicht für Ausbilder — Einträge sind nicht bearbeitbar.' }}
            </span> <span>{{ mb_strlen($day->taetigkeiten ?? '') }} / 600</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          <x-form.field name="dauer"
                        label="Dauer"
                        :value="$day->dauer"
                        :readonly="! $canWrite"
          />
          <x-form.field name="abteilung"
                        label="Abteilung"
                        :value="$day->abteilung"
                        :readonly="! $canWrite"
          />
        </div>

        <div class="border-t border-rule pt-[15px] flex flex-col gap-2.5">
          <div class="flex items-baseline justify-between gap-3">
            <span class="text-[12.5px] text-ink-soft">Lernschritte aus dem Ausbildungsplan</span>
            <span class="text-[12px] text-ink-soft">{{ $day->learningSteps->count() }} zugeordnet</span>
          </div>

          <div class="flex flex-wrap gap-[7px]">
            @foreach ($learningSteps as $schritt)
              <x-kalender.plan-chip :schritt="$schritt"
                                    :checked="$day->learningSteps->contains($schritt)"
                                    :disabled="! $canWrite"
              />
            @endforeach
          </div>

          <p class="text-[12px] leading-snug text-ink-soft">
            Zugeordnete Schritte erscheinen später im Wochenbericht — eine eigene Planseite brauchst du dafür
            nicht. </p>
        </div>

        <div class="mt-auto border-t border-rule pt-[15px] flex flex-wrap items-center justify-between gap-3.5">
          <span class="text-[12px] text-ink-soft">
              @if ($day->updated_at)
              Zuletzt gespeichert {{ $day->updated_at->isoFormat('HH:mm') }}
            @endif
          </span>

          @if ($canWrite)
            <div class="flex gap-[9px]">
              <x-button variant="secondary"
                        size="sm"
                        :href="route('tagesbericht', ['date' => $day->date->copy()->addDay()->toDateString()])"
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
