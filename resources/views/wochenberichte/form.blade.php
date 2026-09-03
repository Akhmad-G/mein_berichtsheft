<div class="py-8 max-w-4xl mx-auto">
  <div class="mb-6 bg-gray-800 rounded text-gray-700 dark:text-gray-300">
    <p><span class="text-gray-400">Name:</span> {{ auth()->user()->name }}</p>
    <p><span class="text-gray-400">Ausbildungsberuf:</span> {{ auth()->user()->ausbildungsberuf ?? '—' }}</p>
    <p><span class="text-gray-400">Ausbildungsbetrieb:</span> {{ auth()->user()->ausbildungsbetrieb ?? '—' }}</p>
    <p class="text-xs text-gray-500 mt-2">Änderungen an diesen Angaben im Profil vornehmen.</p>
  </div>
  
  <div class="mb-6">
    <x-input-label for="week" :value="__('Kalenderwoche')" />
    <input type="week" id="week" required value="{{ now()->format('o-\WW') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
  </div>
  
  <button type="button" id="uebernehmen-btn"
          class="mb-6 px-4 py-2 bg-gray-700 rounded hover:bg-gray-600">
    Aus Tagesberichten übernehmen
  </button>
  
{{--  <x-primary-button id="uebernehmen-btn" class="mb-4">{{ __('Aus Tagesberichten übernehmen') }}</x-primary-button>--}}
  
  <form method="POST" action="{{ route('wochenberichte.store') }}" id="wochenbericht-form">
    @csrf
    <input type="hidden" name="week" id="week-hidden">
    
    @foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'] as $tag)
      <div class="mb-6 p-4 border border-gray-700 rounded">
        <h3 class="font-semibold mb-3 text-white text-center">{{ $tag }}</h3>
        
        <x-input-label for="taetigkeiten" :value="__('Tätigkeiten')" />
        <textarea name="tage[{{ $tag }}][taetigkeiten]" id="taetigkeiten" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
        

        <x-input-label for="gelernt" :value="__('Was habe ich gelernt?')" />
        <textarea name="tage[{{ $tag }}][gelernt]" id="gelernt" cols="30" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
        <x-input-error :messages="$errors->get('gelernt')" class="mt-2" />

{{--        <x-input-label for="gelernt" :value="__('Was habe ich gelernt?')" />--}}
{{--        <textarea name="tage[{{ $tag }}][gelernt]" id="gelernt" rows="2" class="w-full mb-2"></textarea>--}}
        
        <x-input-label for="probleme" :value="__('Besondere Ereignisse / Probleme')" />
        <textarea name="tage[{{ $tag }}][probleme]" id="probleme" cols="30" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
        <x-input-error :messages="$errors->get('probleme')" class="mt-2" />
      </div>
    @endforeach
    
    <button type="submit" class="px-4 py-2 bg-white text-black rounded">
      Wochenbericht speichern
    </button>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const weekInput = document.getElementById('week');
    const weekHidden = document.getElementById('week-hidden');
    const uebernehmenBtn = document.getElementById('uebernehmen-btn');
    const wochentage = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];

    weekInput.addEventListener('change', function () {
      weekHidden.value = weekInput.value;
    });

    uebernehmenBtn.addEventListener('click', async function () {
      if (!weekInput.value) {
        alert('Bitte zuerst eine Kalenderwoche auswählen.');
        return;
      }

      weekHidden.value = weekInput.value;

      uebernehmenBtn.disabled = true;
      uebernehmenBtn.textContent = 'Wird geladen...';

      const url = `{{ route('wochenberichte.uebernehmen') }}?week=${weekInput.value}`;
      console.log('Request URL:', url);
      
      try {
        const response = await fetch(url, {
          headers: { 'Accept': 'application/json' }
        });

        console.log('Status:', response.status);

        const rawText = await response.clone().text();
        console.log('Raw response:', rawText);

        if (!response.ok) throw new Error('Fehler beim Laden' + response.status);

        const data = await response.json();
        console.log('Parsed data:', data);

        wochentage.forEach(function (tag) {
          const form = document.getElementById('wochenbericht-form');
          form.querySelector(`[name="tage[${tag}][taetigkeiten]"]`).value = data[tag]?.taetigkeiten ?? '';
          form.querySelector(`[name="tage[${tag}][gelernt]"]`).value = data[tag]?.gelernt ?? '';
          form.querySelector(`[name="tage[${tag}][probleme]"]`).value = data[tag]?.probleme ?? '';
        });
      } catch (e) {
        console.error('Fetch error:', e);
        alert('Tagesberichte konnten nicht geladen werden.');
      } finally {
        uebernehmenBtn.disabled = false;
        uebernehmenBtn.textContent = 'Aus Tagesberichten übernehmen';
      }
    });
  });
</script>