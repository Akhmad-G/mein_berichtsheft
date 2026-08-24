<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
      
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
    </form>
</section>
