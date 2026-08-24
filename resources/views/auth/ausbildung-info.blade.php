<x-guest-layout>
  
  <h1 style="color: #fff; text-align: center">Info für Berichtsheft</h1>
    <form method="POST" action="{{ route('ausbildung-info.store') }}">
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
      
        <div class="flex items-center justify-end mt-4">
          <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
            {{ __('Already registered?') }}
          </a>
          
          <x-primary-button class="ms-4">
            {{ __('Register') }}
          </x-primary-button>
        </div>
    </form>
</x-guest-layout>
