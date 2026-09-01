<form method="post" action="{{ route('tagesberichte.store') }}" class="mt-6 space-y-6">
  @csrf
  
  <div>
    <x-input-label for="date" :value="__('Datum')" />
    <input type="date" id="date" name="date" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
    <x-input-error class="mt-2" :messages="$errors->get('date')" />
  </div>
  
  <div>
    <x-input-label for="wochentag" :value="__('Wochentag')" />
    <x-text-input type="text" id="wochentag" name="wochentag" class="block mt-1 w-full" readonly/>
    <x-input-error :messages="$errors->get('wochentag')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungsjahr" :value="__('Ausbildungsjahr')" />
    <input type="number" id="ausbildungsjahr" name="ausbildungsjahr" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" readonly/>
    <x-input-error :messages="$errors->get('ausbildungsjahr')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungswoche" :value="__('Ausbildungswoche')" />
    <x-text-input type="text" id="ausbildungswoche" name="ausbildungswoche" class="block mt-1 w-full" readonly/>
    <x-input-error :messages="$errors->get('ausbildungswoche')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="taetigkeiten" :value="__('Tätigkeiten')" />
    <textarea name="taetigkeiten" id="taetigkeiten" cols="30" rows="10" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
    <x-input-error :messages="$errors->get('taetigkeiten')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="gelernt" :value="__('Was habe ich gelernt?')" />
    <textarea name="gelernt" id="gelernt" cols="30" rows="10" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
    <x-input-error :messages="$errors->get('gelernt')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="probleme" :value="__('Besondere Ereignisse / Probleme')" />
    <textarea name="probleme" id="probleme" cols="30" rows="10" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
    <x-input-error :messages="$errors->get('probleme')" class="mt-2" />
  </div>

  
  <div class="flex items-center gap-4">
    <x-primary-button>{{ __('Save') }}</x-primary-button>
    
    @if (session('status') === 'profile-updated')
      <p
        x-data="{ show: true }"
        x-show="show"
        x-transition
        x-init="setTimeout(() => show = false, 2000)"
        class="text-sm text-gray-600 dark:text-gray-400"
      >{{ __('Saved.') }}</p>
    @endif
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dateInput = document.getElementById('date');
      const wochentagInput = document.getElementById('wochentag');
      const ausbildungsjahrInput = document.getElementById('ausbildungsjahr');
      const ausbildungswocheInput = document.getElementById('ausbildungswoche');

      const ausbildungsbeginn = @json(auth()->user()->ausbildungsbeginn?->format('Y-m-d'));

      const wochentage = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

      function getIsoWeek(date) {
        // Datum kopieren, Uhrzeit zurücksetzen, damit es den Vergleich nicht beeinträchtigt.
        const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        // ISO week: the Thursday of the current week determines the week's year.
        const dayNum = d.getUTCDay() || 7;
        d.setUTCDate(d.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        return { week: weekNo, year: d.getUTCFullYear() };
      }

      function updateFields() {
        if (!dateInput.value) return;

        const selectedDate = new Date(dateInput.value + 'T00:00:00');

        // Wochentag
        wochentagInput.value = wochentage[selectedDate.getDay()];

        // Ausbildungswoche — Kalenderwoche, Format "KW36, 2026"
        const { week, year } = getIsoWeek(selectedDate);
        ausbildungswocheInput.value = `KW${week}, ${year}`;

        // Ausbildungsjahr — wir zählen ab dem Beginn der Ausbildung (sofern ein solcher Termin besteht).
        if (!ausbildungsbeginn) {
          ausbildungsjahrInput.value = '';
          return;
        }

        const startDate = new Date(ausbildungsbeginn + 'T00:00:00');

        if (selectedDate < startDate) {
          ausbildungsjahrInput.value = 0;
          return;
        }

        let jahr = selectedDate.getFullYear() - startDate.getFullYear();
        const anniversaryThisYear = new Date(startDate);
        anniversaryThisYear.setFullYear(startDate.getFullYear() + jahr);
        if (selectedDate < anniversaryThisYear) {
          jahr -= 1;
        }
        ausbildungsjahrInput.value = jahr + 1;
      }

      dateInput.addEventListener('change', updateFields);

      if (dateInput.value) {
        updateFields();
      }
    });
  </script>
</form>