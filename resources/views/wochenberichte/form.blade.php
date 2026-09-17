@php
  use Carbon\Carbon;$report ??= [];
  $method ??= 'POST';
  $readonly ??= false;
  $autoload ??= true;
  $submitLabel ??= __('Wochenbericht speichern');

  $weekValue = old('week', isset($report['week_start'])
      ? Carbon::parse($report['week_start'])->format('o-\WW')
      : now()->format('o-\WW'));

  $wochentage = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];
@endphp

<div class="py-8 max-w-4xl mx-auto">
  <div class="mb-6 bg-gray-800 rounded text-gray-700 dark:text-gray-300">
    <p><span class="text-gray-400">Name:</span> {{ auth()->user()->name }}</p>
    <p><span class="text-gray-400">Ausbildungsberuf:</span> {{ auth()->user()->ausbildungsberuf ?? '—' }}</p>
    <p><span class="text-gray-400">Ausbildungsbetrieb:</span> {{ auth()->user()->ausbildungsbetrieb ?? '—' }}</p>
    <p class="text-xs text-gray-500 mt-2">Änderungen an diesen Angaben im Profil vornehmen.</p>
  </div>

  <div class="mb-6">
    <x-input-label for="week"
                   :value="__('Kalenderwoche')"
    />
    <input type="week"
           id="week"
           required
           value="{{ $weekValue }}"
           @readonly($readonly)
           class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    >
  </div>

  <form method="POST"
        action="{{ $action }}"
        id="wochenbericht-form"
  >
    @csrf

    @if (! in_array(strtoupper($method), ['GET', 'POST']))
      @method($method)
    @endif

    <input type="hidden"
           name="week"
           id="week-hidden"
           value="{{ $weekValue }}"
    >

    @foreach ($wochentage as $tag)
      <div class="mb-6 p-4 border border-gray-700 rounded">
        <h3 class="font-semibold mb-3 text-white text-center">{{ $tag }}</h3>

        <x-input-label for="taetigkeiten-{{ $tag }}"
                       :value="__('Tätigkeiten')"
        />
        <x-textarea-input name="tage[{{ $tag }}][taetigkeiten]"
                          id="taetigkeiten-{{ $tag }}"
                          rows="4"
                          :readonly="$readonly"
        >{{ old("tage.$tag.taetigkeiten", $report['tage'][$tag]['taetigkeiten'] ?? '') }}</x-textarea-input>

        <x-input-label for="gelernt-{{ $tag }}"
                       :value="__('Was habe ich gelernt?')"
        />
        <x-textarea-input name="tage[{{ $tag }}][gelernt]"
                          id="gelernt-{{ $tag }}"
                          cols="30"
                          rows="4"
                          :readonly="$readonly"
        >{{ old("tage.$tag.gelernt", $report['tage'][$tag]['gelernt'] ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('gelernt')"
                       class="mt-2"
        />

        <x-input-label for="probleme-{{ $tag }}"
                       :value="__('Besondere Ereignisse / Probleme')"
        />
        <x-textarea-input name="tage[{{ $tag }}][probleme]"
                          id="probleme-{{ $tag }}"
                          cols="30"
                          rows="4"
                          :readonly="$readonly"
        >{{ old("tage.$tag.probleme", $report['tage'][$tag]['probleme'] ?? '') }}</x-textarea-input>
        <x-input-error :messages="$errors->get('probleme')"
                       class="mt-2"
        />
      </div>
    @endforeach

    @unless($readonly)
      <button type="submit"
              class="px-4 py-2 bg-white text-black rounded"
      >
        {{ $submitLabel }}
      </button>
    @endunless
  </form>
</div>

@if(! $readonly && $autoload)
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const weekInput = document.getElementById('week');
      const weekHidden = document.getElementById('week-hidden');
      const form = document.getElementById('wochenbericht-form');
      const wochentage = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];

      weekInput.addEventListener('change', function () {
        weekHidden.value = weekInput.value;
      });

      async function ladeTagesberichte() {
        if (!weekInput.value) {
          return;
        }

        weekHidden.value = weekInput.value;

        const url = `{{ route('wochenberichte.uebernehmen') }}?week=${weekInput.value}`;

        try {
          const response = await fetch(url, {
            headers: {'Accept': 'application/json'}
          });

          if (!response.ok) {
            throw new Error('Fehler beim Laden: ' + response.status);
          }

          const data = await response.json();

          wochentage.forEach(function (tag) {
            form.querySelector(`[name="tage[${tag}][taetigkeiten]"]`).value = data[tag]?.taetigkeiten ?? '';
            form.querySelector(`[name="tage[${tag}][gelernt]"]`).value = data[tag]?.gelernt ?? '';
            form.querySelector(`[name="tage[${tag}][probleme]"]`).value = data[tag]?.probleme ?? '';
          });
        } catch (e) {
          console.error(e);
        }
      }

      weekInput.addEventListener('change', ladeTagesberichte);

      ladeTagesberichte();
    });
  </script>
@endif