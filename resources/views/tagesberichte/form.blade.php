<form method="post" action="{{ route('tagesberichte.store') }}" class="mt-6 space-y-6">
  @csrf
  
  <div>
    <x-input-label for="date" :value="__('Datum')" />
    <input type="date" name="date" id="date" value="" class="mt-1 block w-full">
{{--    <x-text-input id="email" name="email" type="email"  :value="old('email', $user->email)" required autocomplete="username" />--}}
    <x-input-error class="mt-2" :messages="$errors->get('date')" />
  </div>
  <div>
{{--
    <form>
      <label for="week">Select a week:</label>
      <input type="week" id="week" name="week">       ---> Besser für Ausbildungswoche
    </form>
    
    OR
    
    <label for="cars">Choose a car:</label>
    <select id="cars" name="cars">
      <option value="volvo">Volvo</option>
      <option value="saab">Saab</option>
      <option value="fiat">Fiat</option>
      <option value="audi">Audi</option>
    </select>
--}}
    <x-input-label for="wochentag" :value="__('Wochentag')" />
    <x-text-input id="wochentag" class="block mt-1 w-full" type="text" name="wochentag" required autofocus autocomplete="wochentag" />
    <x-input-error :messages="$errors->get('wochentag')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungsjahr" :value="__('Ausbildungsjahr')" />
    <x-text-input id="ausbildungsjahr" class="block mt-1 w-full" type="text" name="ausbildungsjahr" required autofocus autocomplete="ausbildungsjahr" />
    <x-input-error :messages="$errors->get('ausbildungsjahr')" class="mt-2" />
  </div>
  
  <div>
    <x-input-label for="ausbildungswoche" :value="__('Ausbildungswoche')" />
    <x-text-input id="ausbildungswoche" class="block mt-1 w-full" type="text" name="ausbildungswoche" required autofocus autocomplete="ausbildungswoche" />
    <x-input-error :messages="$errors->get('ausbildungswoche')" class="mt-2" />
  </div>
  
{{--
  
  <!-- Vorname -->
  <div>
    <x-input-label for="vorname" :value="__('Vorname')" />
    <x-text-input id="vorname" class="block mt-1 w-full" type="text" name="vorname" :value="old('vorname', $user->vorname)" required autofocus autocomplete="vorname" />
    <x-input-error :messages="$errors->get('vorname')" class="mt-2" />
  </div>
  
  <!-- Nachname -->
  <div>
    <x-input-label for="nachname" :value="__('Nachname')" />
    <x-text-input id="nachname" class="block mt-1 w-full" type="text" name="nachname" :value="old('nachname', $user->nachname)" required autofocus autocomplete="nachname" />
    <x-input-error :messages="$errors->get('nachname')" class="mt-2" />
  </div>
  
  <!-- Ausbildungsberuf -->
  <div>
    <x-input-label for="ausbildungsberuf" :value="__('Ausbildungsberuf')" />
    <x-text-input id="ausbildungsberuf" class="block mt-1 w-full" type="text" name="ausbildungsberuf" :value="old('ausbildungsberuf', $user->ausbildungsberuf)" required autofocus autocomplete="ausbildungsberuf" />
    <x-input-error :messages="$errors->get('ausbildungsberuf')" class="mt-2" />
  </div>
  
  <!-- Ausbildungsbetrieb -->
  <div>
    <x-input-label for="ausbildungsbetrieb" :value="__('Ausbildungsbetrieb')" />
    <x-text-input id="ausbildungsbetrieb" class="block mt-1 w-full" type="text" name="ausbildungsbetrieb" :value="old('ausbildungsbetrieb', $user->ausbildungsbetrieb)" required autofocus autocomplete="ausbildungsbetrieb" />
    <x-input-error :messages="$errors->get('ausbildungsbetrieb')" class="mt-2" />
  </div>
  
  <!-- Ausbildungsbeginn -->
  <div>
    <x-input-label for="ausbildungsbeginn" :value="__('Ausbildungsbeginn')" />
    <x-text-input id="ausbildungsbeginn" class="block mt-1 w-full" type="date" name="ausbildungsbeginn" :value="old('ausbildungsbeginn', $user->ausbildungsbeginn?->format('Y-m-d'))" required autofocus autocomplete="ausbildungsbeginn" />
    <x-input-error :messages="$errors->get('ausbildungsbeginn')" class="mt-2" />
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
--}}
</form>