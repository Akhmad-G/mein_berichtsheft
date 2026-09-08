@php
    $report ??= [];
    $method ??= 'POST';
    $readonly ??= false;
    $submitLabel ??= __('Tagesbericht speichern');
@endphp

<form method="post" action="{{ $action }}" class="mt-6 space-y-6">
    @csrf
    
    @if(! in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif
    
    <x-report-section title="Datum">
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V6.75A2.25 2.25 0 0 0 18.75 4.5H5.25A2.25 2.25 0 0 0 3 6.75v12A2.25 2.25 0 0 0 5.25 21Z" />
            </svg>
        </x-slot>
        
        <input
            type="date"
            id="date"
            name="date"
            value="{{ old('date', $report['date'] ?? now()->format('Y-m-d')) }}"
            @readonly($readonly)
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        >
        <x-input-error class="mt-2" :messages="$errors->get('date')" />
    </x-report-section>
    
    <input type="hidden" id="wochentag" name="wochentag" value="{{ old('wochentag', $report['wochentag'] ?? '') }}">
    <input type="hidden" id="ausbildungsjahr" name="ausbildungsjahr" value="{{ old('ausbildungsjahr', $report['ausbildungsjahr'] ?? '') }}">
    <input type="hidden" id="ausbildungswoche" name="ausbildungswoche" value="{{ old('ausbildungswoche', $report['ausbildungswoche'] ?? '') }}">
    
    <x-report-meta-card
        title="Berichtsinformationen"
        :items="[
          ['label' => 'Wochentag', 'value' => old('wochentag', $report['wochentag'] ?? '—'), 'id' => 'wochentag-display'],
          ['label' => 'Ausbildungsjahr', 'value' => old('ausbildungsjahr', $report['ausbildungsjahr'] ?? '—'), 'id' => 'ausbildungsjahr-display'],
          ['label' => 'Ausbildungswoche', 'value' => old('ausbildungswoche', $report['ausbildungswoche'] ?? '—'), 'id' => 'ausbildungswoche-display'],
        ]"
    >
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
        </x-slot>
    </x-report-meta-card>
    
    <x-report-section title="Tätigkeiten">
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75Zm0 5.25h.008v.008H3.75V12Zm0 5.25h.008v.008H3.75v-.008Z" />
            </svg>
        </x-slot>
        
        <x-textarea-input
            name="taetigkeiten"
            id="taetigkeiten"
            cols="30"
            rows="10"
            :readonly="$readonly"
        >{{ old('taetigkeiten', $report['taetigkeiten'] ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('taetigkeiten')" class="mt-2" />
    </x-report-section>
    
    <x-report-section title="Was habe ich gelernt?">
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6.75 6.75 0 0 0 6.75-6.75V6.75A2.25 2.25 0 0 0 16.5 4.5h-9A2.25 2.25 0 0 0 5.25 6.75V12A6.75 6.75 0 0 0 12 18.75Zm0 0v2.25m-3.75 0h7.5" />
            </svg>
        </x-slot>
        
        <x-textarea-input
            name="gelernt"
            id="gelernt"
            cols="30"
            rows="10"
            :readonly="$readonly"
        >{{ old('gelernt', $report['gelernt'] ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('gelernt')" class="mt-2" />
    </x-report-section>
    
    <x-report-section title="Besondere Ereignisse / Probleme">
        <x-slot name="icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.731 0-2.814-1.874-1.948-3.374l7.355-12.74c.866-1.5 3.03-1.5 3.896 0l7.355 12.74ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </x-slot>
        
        <x-textarea-input
            name="probleme"
            id="probleme"
            cols="30"
            rows="10"
            :readonly="$readonly"
        >{{ old('probleme', $report['probleme'] ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('probleme')" class="mt-2" />
    </x-report-section>
    
    @unless($readonly)
        <div class="flex items-center gap-4">
            <x-primary-button>{{ $submitLabel }}</x-primary-button>
            
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
    @endunless
    
    @unless($readonly)
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('date');
    
            const wochentagInput = document.getElementById('wochentag');
            const ausbildungsjahrInput = document.getElementById('ausbildungsjahr');
            const ausbildungswocheInput = document.getElementById('ausbildungswoche');
    
            const wochentagDisplay = document.getElementById('wochentag-display');
            const ausbildungsjahrDisplay = document.getElementById('ausbildungsjahr-display');
            const ausbildungswocheDisplay = document.getElementById('ausbildungswoche-display');
    
            const ausbildungsbeginn = @json(auth()->user()->ausbildungsbeginn?->format('Y-m-d'));
    
            const wochentage = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
    
            function getIsoWeek(date) {
              const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
              const dayNum = d.getUTCDay() || 7;
              d.setUTCDate(d.getUTCDate() + 4 - dayNum);
              const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
              const weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    
              return { week: weekNo, year: d.getUTCFullYear() };
            }
    
            function updateFields() {
              if (!dateInput.value) return;
              
              const selectedDate = new Date(dateInput.value + 'T00:00:00');
    
              const wochentag = wochentage[selectedDate.getDay()];
              const { week, year } = getIsoWeek(selectedDate);
              const ausbildungswoche = `KW${week}, ${year}`;
    
              let ausbildungsjahr = '';
    
              if (ausbildungsbeginn) {
                const startDate = new Date(ausbildungsbeginn + 'T00:00:00');
    
                if (selectedDate < startDate) {
                  ausbildungsjahr = 0;
                } else {
                  let jahr = selectedDate.getFullYear() - startDate.getFullYear();
                  const anniversaryThisYear = new Date(startDate);
                  anniversaryThisYear.setFullYear(startDate.getFullYear() + jahr);
    
                  if (selectedDate < anniversaryThisYear) {
                    jahr -= 1;
                  }
    
                  ausbildungsjahr = jahr + 1;
                }
              }
    
              wochentagInput.value = wochentag;
              ausbildungsjahrInput.value = ausbildungsjahr;
              ausbildungswocheInput.value = ausbildungswoche;
    
              wochentagDisplay.textContent = wochentag || '—';
              ausbildungsjahrDisplay.textContent = ausbildungsjahr || '—';
              ausbildungswocheDisplay.textContent = ausbildungswoche || '—';
            }
    
            dateInput.addEventListener('change', updateFields);
    
            if (dateInput.value) {
              updateFields();
            }
          });
        </script>
    @endunless
</form>