<x-layouts.app title="Kalender">

  <x-notebook.tabs current="calendar"
                   :items="$tabs"
  />

  <x-notebook>
    {{-- left page: month --}}
    <x-slot:left>
      <x-notebook.page-header :title="$month->isoFormat('MMMM YYYY')" tight>
        <x-slot:actions>
          <x-notebook.stepper :prev="route('calendar', ['day' => $month->subMonth()->toDateString()])"
                              :next="route('calendar', ['day' => $month->addMonth()->toDateString()])"
                              :jump="route('calendar')"
                              jump-label="Heute"
          />
        </x-slot:actions>
      </x-notebook.page-header>

      <x-calendar.month :cells="$cells"
                        :selected="$day->date"
                        :today="$today"
      />
    </x-slot:left>

    {{-- right page: day sheet --}}
    <x-slot:right>
      <x-calendar.week-strip :days="$week->days"
                             :selected="$day->date"
                             :note="'KW ' . $week->week . ' · ' . $week->recordedDays . '/5 Tage'"
      />

      <form method="POST"
            action="{{ route('day-reports.update', ['date' => $day->date->toDateString()]) }}"
            class="px-[22px] py-5 flex flex-col gap-4 flex-1"
      >
        @csrf
        @method('PUT')

        <div class="flex flex-wrap items-baseline justify-between gap-3.5">
          <h2 class="font-display text-[21px]">{{ $day->date->isoFormat('dddd, DD.MM.YYYY') }}</h2>
          <x-stamp :status="$day->stamp()"
                   size="md"
          >{{ $day->statusLabel() }}</x-stamp>
        </div>

        <x-calendar.day-type :day="$day" :count="$typeCount" :disabled="! $canEdit" />

        {{-- work-day fields; hidden by JS when another day type is picked --}}
        <div data-day-type-work @if (! $day->type->isWork()) hidden @endif class="flex flex-col gap-4">

          <div class="flex flex-col gap-[7px]">
            <x-form.label for="activities">Tätigkeiten</x-form.label>
            <textarea id="activities"
                      name="activities"
                      rows="8"
                      maxlength="600"
                      @readonly(!$canEdit)
                      placeholder="Ein Satz je Tätigkeit. Stichpunkte genügen."
                      class="w-full bg-paper-line border border-rule rounded-md px-3 py-2.5 text-[14.5px]
                             leading-relaxed resize-y focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink
                             read-only:text-ink-soft"
            >{{ old('activities', $day->activities) }}</textarea>

            <div class="flex justify-between gap-3 text-[12px] text-ink-soft">
              <span>{{ $canEdit
                  ? 'Der Ausbilder liest den Wortlaut genau so.'
                  : 'Ansicht für Ausbilder — Einträge sind nicht bearbeitbar.' }}
              </span>
              <span>{{ mb_strlen($day->activities ?? '') }} / 600</span>
            </div>
            <x-form.error :messages="$errors->get('activities')" />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <x-form.field name="duration"
                          label="Dauer"
                          :value="$day->duration"
                          :readonly="! $canEdit"
            />
            <x-form.field name="department"
                          label="Abteilung"
                          :value="$day->department ?? auth()->user()->abteilung"
                          :readonly="! $canEdit"
            />
          </div>

          @if ($learningSteps)
            <div class="border-t border-rule pt-[15px] flex flex-col gap-2.5">
              <div class="flex items-baseline justify-between gap-3">
                <span class="text-[12.5px] text-ink-soft">Lernschritte aus dem Ausbildungsplan</span>
                <span class="text-[12px] text-ink-soft">{{ count($day->learningSteps) }} zugeordnet</span>
              </div>

              <div class="flex flex-wrap gap-[7px]">
                @foreach ($learningSteps as $step)
                  <x-calendar.learning-step-chip
                    :step="$step"
                    :checked="in_array($step['id'], old('learning_steps', $day->learningSteps), true)"
                    :disabled="! $canEdit"
                  />
                @endforeach
              </div>

              <p class="text-[12px] leading-snug text-ink-soft">
                Zugeordnete Schritte erscheinen später im Wochenbericht — eine eigene Planseite brauchst du dafür nicht.
              </p>
            </div>
          @endif
        </div>

        @if ($canEdit)
          @php
            $next = $day->date->addWeekday();
          @endphp
          <div class="mt-auto border-t border-rule pt-[15px] flex flex-wrap items-center justify-end gap-[9px]">
            <x-button variant="secondary" size="sm" :href="route('calendar', ['day' => $next->toDateString()])">
              Nächster Tag
            </x-button>
            <x-button size="sm" data-day-type-submit>
              {{ $day->type->isWork() ? 'Speichern' : $day->type->label() . ' speichern' }}
            </x-button>
          </div>
        @endif
      </form>
    </x-slot:right>
  </x-notebook>

</x-layouts.app>
