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

          <div class="grid grid-cols-1 sm:grid-cols-[120px_minmax(0,1fr)] gap-3.5">
            <div class="flex flex-col gap-[7px]">
              <x-form.label for="duration">Dauer</x-form.label>
              <select id="duration"
                      name="duration"
                      @disabled(! $canEdit)
                      class="w-full bg-paper-line border border-rule rounded-md px-3 py-2 text-[14px]
                                 focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink disabled:text-ink-soft"
              >
                @foreach (['8 St.', '7.5 St.', '7 St.', '6.5 St.','6 St.', '5.5 St.', '5 St.', '4.5 St.', '4 St.', '3.5 St.',
                           '3 St.', '2.5 St.', '2 St.', '1.5 St.', '1 St.'] as $duration)
                  <option value="{{ $duration }}" @selected(old('duration', $day->duration) === $duration)>
                    {{ $duration }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="flex flex-col gap-[7px]" data-learning-steps>
              <x-form.label>Lernschritte aus dem Ausbildungsplan</x-form.label>

              @php
                $selectedLearningSteps = old('learning_steps', $day->learningSteps);
              @endphp

              <button type="button"
                      data-learning-steps-button
                      @disabled(! $canEdit || ! $learningSteps)
                      class="w-full bg-paper-line border border-rule rounded-md px-3 py-2 text-[14px] text-left
                                 focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink disabled:text-ink-soft"
              >
                    <span data-learning-steps-label>
                      {{ count($selectedLearningSteps) ? count($selectedLearningSteps) . ' ausgewählt' : 'Bitte auswählen' }}
                    </span>
              </button>

              <div data-learning-steps-panel
                   hidden
                   class="relative"
              >
                <div class="absolute bottom-full z-30 mb-1 max-h-[220px] w-full overflow-y-auto rounded-md border border-rule bg-paper shadow-lg">
                  @foreach ($learningSteps as $step)
                    <label class="flex gap-2 px-3 py-2 text-[13px] hover:bg-paper-raised cursor-pointer">
                      <input type="checkbox"
                             name="learning_steps[]"
                             value="{{ $step['id'] }}"
                             @checked(in_array($step['id'], $selectedLearningSteps, true))
                             @disabled(! $canEdit)
                             data-learning-steps-checkbox
                             class="rounded-[4px] border-rule bg-paper-line text-[#20262C]
                                    focus:ring-ink focus:ring-offset-0
                                    checked:bg-[#20262C] checked:border-[#20262C]
                                    disabled:opacity-50"
                      >
                      <span>{{ $step['id'] }} · {{ $step['label'] }}</span>
                    </label>
                  @endforeach
                </div>
              </div>

              <x-form.error :messages="$errors->get('learning_steps')" />
          </div>

{{--          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">--}}
{{--            <x-form.field name="duration"--}}
{{--                          label="Dauer"--}}
{{--                          :value="$day->duration"--}}
{{--                          :readonly="! $canEdit"--}}
{{--            />--}}
{{--            <x-form.field name="department"--}}
{{--                          label="Abteilung"--}}
{{--                          :value="$day->department ?? auth()->user()->abteilung"--}}
{{--                          :readonly="! $canEdit"--}}
{{--            />--}}
{{--          </div>--}}
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
