<x-layouts.auth title="E-Mail bestätigen">

  <div class="border border-rule rounded-xl overflow-hidden bg-paper shadow-[0_18px_40px_-26px_rgba(32,38,44,.28)] max-w-xl mx-auto">

    <x-auth.header current="verify-email" />

    <div class="p-7 sm:p-8 flex flex-col gap-5">
      <div>
        <div class="w-9 h-0.5 bg-stamp mb-4"></div>
        <h1 class="font-display text-[22px]">E-Mail-Adresse bestätigen</h1>
        <p class="mt-2 text-[14px] leading-relaxed text-ink-soft">
          Vielen Dank für deine Registrierung! Bevor es losgeht: Klicke bitte auf den Bestätigungslink, den wir dir per E-Mail geschickt haben. Falls du keine E-Mail erhalten hast, senden wir dir gerne eine neue.
        </p>
      </div>

      @if (session('status') == 'verification-link-sent')
        <div class="border border-signed bg-signed-soft text-signed rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
          Ein neuer Bestätigungslink wurde an deine angegebene E-Mail-Adresse gesendet.
        </div>
      @endif

      <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
        <form method="POST"
              action="{{ route('verification.send') }}"
        >
          @csrf
          <x-button>Bestätigungs-Link erneut senden</x-button>
        </form>

        <form method="POST"
              action="{{ route('logout') }}"
        >
          @csrf
          <button type="submit"
                  class="text-[13px] text-ink-soft hover:text-ink underline decoration-rule underline-offset-4"
          >
            Abmelden
          </button>
        </form>
      </div>
    </div>
  </div>

</x-layouts.auth>
