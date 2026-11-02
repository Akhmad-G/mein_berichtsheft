<x-layouts.auth title="Passwort bestätigen">

  <div class="border border-rule rounded-xl overflow-hidden bg-paper shadow-[0_18px_40px_-26px_rgba(32,38,44,.28)] max-w-xl mx-auto">

    <x-auth.header current="confirm-password" />

    <div class="p-7 sm:p-8 flex flex-col gap-5">
      <div>
        <div class="w-9 h-0.5 bg-stamp mb-4"></div>
        <h1 class="font-display text-[22px]">Sicherheitsbereich</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-ink-soft">
          Dies ist ein geschützter Bereich der Anwendung. Bitte bestätige dein Passwort, bevor du fortfährst.
        </p>
      </div>

      <form method="POST"
            action="{{ route('password.confirm') }}"
            class="flex flex-col gap-4"
      >
        @csrf

        <x-form.field name="password"
                      label="Passwort"
                      type="password"
                      required
                      autofocus
                      autocomplete="current-password"
                      placeholder="••••••••"
        />

        <div class="flex items-center justify-end pt-2">
          <x-button>Bestätigen</x-button>
        </div>
      </form>
    </div>
  </div>

</x-layouts.auth>
