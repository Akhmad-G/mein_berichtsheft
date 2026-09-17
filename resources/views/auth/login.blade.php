<x-layouts.auth title="Anmelden">

  <div class="border border-rule rounded-xl overflow-hidden bg-paper shadow-[0_18px_40px_-26px_rgba(32,38,44,.28)]">

    <x-auth.header current="login" />

    <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)]">

      {{-- Papierseite --}}
      <div class="bg-paper-line border-b md:border-b-0 md:border-r border-rule px-7 py-8 flex flex-col justify-between gap-8 md:min-h-[430px]">
        <div>
          <div class="w-9 h-0.5 bg-stamp mb-4"></div>
          <h1 class="font-display text-[29px] leading-[1.18] max-w-[300px] text-pretty">Willkommen zurück.</h1>
          <p class="mt-3.5 text-[15px] leading-relaxed text-ink-soft max-w-[300px] text-pretty">
            Melde dich an, um deine Tagesberichte fortzuschreiben und den Wochenbericht abzuschließen. </p>
        </div>

        <x-auth.week-preview :filled="4" />
      </div>

      {{-- Formular --}}
      <div class="px-7 py-8 flex flex-col justify-center gap-4.5">
        <h2 class="font-display text-[21px]">Anmelden</h2>

        @if (session('status'))
          <div class="border border-signed bg-signed-soft text-signed rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
            {{ session('status') }}
          </div>
        @endif

        @error('email')
        <div class="border border-stamp bg-stamp-soft text-stamp rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
          Diese Zugangsdaten passen nicht zusammen. Prüfe E-Mail und Passwort.
        </div>
        @enderror

        <form method="POST"
              action="{{ route('login') }}"
              class="flex flex-col gap-1"
        >
          @csrf

          <x-form.field name="email"
                        label="E-Mail"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="lena.hartmann@betrieb.de"
          />

          <div class="flex flex-col gap-1.5">
            <div class="flex justify-between items-baseline gap-3">
              <x-form.label for="password">Passwort</x-form.label>

              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-[12.5px] text-ink-soft underline decoration-rule underline-offset-[3px] hover:text-ink"
                > Passwort vergessen? </a>
              @endif
            </div>

            <x-form.input id="password"
                          name="password"
                          type="password"
                          required
                          autocomplete="current-password"
                          placeholder="••••••••"
                          :invalid="$errors->has('password')"
            />

            <x-form.error :messages="$errors->get('password')" />
          </div>

          <x-form.checkbox name="remember"
                           class="mt-1 items-center"
          >Angemeldet bleiben
          </x-form.checkbox>

          <x-button class="mt-3.5">Anmelden</x-button>
        </form>

        <p class="border-t border-rule pt-3 text-[12.5px] text-ink-soft">
          Registrierung ist für Azubis. Ausbilder-Zugänge richtet dein Betrieb ein. </p>
      </div>
    </div>
  </div>

</x-layouts.auth>
