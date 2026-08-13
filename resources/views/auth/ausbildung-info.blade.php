<x-guest-layout>
  
  <h1>Info für Berichtsheft</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
      
        <!-- Vorname -->
        <div>
          <x-input-label for="vorname" :value="__('Vorname')" />
          <x-text-input id="vorname" class="block mt-1 w-full" type="text" name="vorname" :value="old('vorname')" required autofocus autocomplete="vorname" />
          <x-input-error :messages="$errors->get('vorname')" class="mt-2" />
        </div>
      
        <!-- Nachname -->
        <div>
          <x-input-label for="nachname" :value="__('Nachname')" />
          <x-text-input id="nachname" class="block mt-1 w-full" type="text" name="nachname" :value="old('nachname')" required autofocus autocomplete="nachname" />
          <x-input-error :messages="$errors->get('nachname')" class="mt-2" />
        </div>
      
        <!-- Ausbildungsberuf -->
        <div>
          <x-input-label for="ausbildungsberuf" :value="__('Ausbildungsberuf')" />
          <x-text-input id="ausbildungsberuf" class="block mt-1 w-full" type="text" name="ausbildungsberuf" :value="old('ausbildungsberuf')" required autofocus autocomplete="ausbildungsberuf" />
          <x-input-error :messages="$errors->get('ausbildungsberuf')" class="mt-2" />
        </div>
      
        <!-- Ausbildungsbetrieb -->
        <div>
          <x-input-label for="ausbildungsbetrieb" :value="__('Ausbildungsbetrieb')" />
          <x-text-input id="ausbildungsbetrieb" class="block mt-1 w-full" type="text" name="ausbildungsbetrieb" :value="old('ausbildungsbetrieb')" required autofocus autocomplete="ausbildungsbetrieb" />
          <x-input-error :messages="$errors->get('ausbildungsbetrieb')" class="mt-2" />
        </div>
      
        <!-- Ausbildungsbeginn -->
        <div>
          <x-input-label for="ausbildungsbeginn" :value="__('Ausbildungsbeginn')" />
          <x-text-input id="ausbildungsbeginn" class="block mt-1 w-full" type="date" name="ausbildungsbeginn" :value="old('ausbildungsbeginn')" required autofocus autocomplete="ausbildungsbeginn" />
          <x-input-error :messages="$errors->get('ausbildungsbeginn')" class="mt-2" />
        </div>
    </form>
</x-guest-layout>
