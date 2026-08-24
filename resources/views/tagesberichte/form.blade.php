<form method="post" action="{{ route('tagesberichte.store') }}" class="mt-6 space-y-6">
  @csrf
  
  @php
      $wochentage = [
        1 => 'montag',
        2 => 'dienstag',
        3 => 'mittwoch',
        4 => 'donnerstag',
        5 => 'freitag',
        6 => 'samstag',
        7 => 'sonntag',
      ];
      
      $heute = $wochentage[now()->isoWeekday()];
      
      $ausbildungsjahr = (now()->year) - ($user->ausbildungsbeginn?->format('Y'));
  @endphp
  
  
  <div>
    <x-input-label for="date" :value="__('Datum')" />
    <input type="date" name="date" id="date" value="{{ now()->format('Y-m-d') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
    <x-input-error class="mt-2" :messages="$errors->get('date')" />
  </div>
  
  <div>
    <x-input-label for="wochentag" :value="__('Wochentag')" />
    <select id="wochentag" name="wochentag" required autocomplete="wochentag" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
      <option value="montag" @selected($heute === 'montag')>Montag</option>
      <option value="dienstag" @selected($heute === 'dienstag')>Dienstag</option>
      <option value="mittwoch" @selected($heute === 'mittwoch')>Mittwoch</option>
      <option value="donnerstag" @selected($heute === 'donnerstag')>Donnerstag</option>
      <option value="freitag" @selected($heute === 'freitag')>Freitag</option>
    </select>
    <x-input-error :messages="$errors->get('wochentag')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungsjahr" :value="__('Ausbildungsjahr')" />
    <x-text-input id="ausbildungsjahr" class="block mt-1 w-full" type="text" name="ausbildungsjahr" required autofocus autocomplete="ausbildungsjahr" :value="$ausbildungsjahr"/>
    <x-input-error :messages="$errors->get('ausbildungsjahr')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungswoche" :value="__('Ausbildungswoche')" />
    <input type="week" id="ausbildungswoche" name="ausbildungswoche" value="{{ now()->format('o-\WW') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
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
</form>