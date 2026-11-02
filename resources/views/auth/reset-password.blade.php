<x-layouts.auth title="Passwort zurücksetzen">

  <div class="border border-rule rounded-xl overflow-hidden bg-paper shadow-[0_18px_40px_-26px_rgba(32,38,44,.28)] max-w-xl mx-auto">

    <x-auth.header current="reset-password" />

    <div class="p-7 sm:p-8 flex flex-col gap-5">
      <div>
        <div class="w-9 h-0.5 bg-stamp mb-4"></div>
        <h1 class="font-display text-[22px]">Neues Passwort vergeben</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-ink-soft">
          Wähle ein sicheres neues Passwort für deinen Zugang.
        </p>
      </div>

      <form method="POST"
            action="{{ route('password.store') }}"
            class="flex flex-col gap-4"
      >
        @csrf

        <input type="hidden"
               name="token"
               value="{{ $request->route('token') }}"
        >

        <x-form.field name="email"
                      label="E-Mail"
                      type="email"
                      :value="old('email', $request->email)"
                      required
                      autofocus
                      autocomplete="username"
                      placeholder="lena.hartmann@betrieb.de"
        />

        <x-form.field name="password"
                      label="Neues Passwort"
                      type="password"
                      required
                      autocomplete="new-password"
                      placeholder="Mindestens 8 Zeichen"
        />

        <x-form.field name="password_confirmation"
                      label="Passwort bestätigen"
                      type="password"
                      required
                      autocomplete="new-password"
                      placeholder="Passwort wiederholen"
        />

        <div class="flex items-center justify-end pt-2">
          <x-button>Passwort zurücksetzen</x-button>
        </div>
      </form>
    </div>
  </div>

</x-layouts.auth>
