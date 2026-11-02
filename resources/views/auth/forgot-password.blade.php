<x-layouts.auth title="Passwort vergessen">

  <div class="border border-rule rounded-xl overflow-hidden bg-paper shadow-[0_18px_40px_-26px_rgba(32,38,44,.28)] max-w-xl mx-auto">

    <x-auth.header current="forgot-password" />

    <div class="p-7 sm:p-8 flex flex-col gap-5">
      <div>
        <div class="w-9 h-0.5 bg-stamp mb-4"></div>
        <h1 class="font-display text-[22px]">Passwort vergessen?</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-ink-soft">
          Kein Problem. Gib deine E-Mail-Adresse ein und wir senden dir einen Link, mit dem du ein neues Passwort vergeben kannst.
        </p>
      </div>

      @if (session('status'))
        <div class="border border-signed bg-signed-soft text-signed rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST"
            action="{{ route('password.email') }}"
            class="flex flex-col gap-4"
      >
        @csrf

        <x-form.field name="email"
                      label="E-Mail"
                      type="email"
                      :value="old('email')"
                      required
                      autofocus
                      autocomplete="username"
                      placeholder="lena.hartmann@betrieb.de"
        />

        <div class="flex items-center justify-between pt-2">
          <a href="{{ route('login') }}"
             class="text-[13px] text-ink-soft hover:text-ink underline decoration-rule underline-offset-4"
          >
            Zurück zur Anmeldung
          </a>

          <x-button>Link anfordern</x-button>
        </div>
      </form>
    </div>
  </div>

</x-layouts.auth>
