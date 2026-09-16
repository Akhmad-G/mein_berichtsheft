<x-layouts.auth title="Anmelden">

    <div class="border border-rule rounded-xl overflow-hidden grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1.05fr)]">

        {{-- Papierseite --}}
        <div class="bg-paper-line border-b md:border-b-0 md:border-r border-rule p-9 flex flex-col justify-between gap-8">
            <div>
                <x-brand :size="26" :text="17" />

                <div class="w-9 h-0.5 bg-stamp mt-7 mb-4"></div>
                <h1 class="font-display text-[29px] leading-[1.18] max-w-[300px] text-pretty">Willkommen zurück.</h1>
                <p class="mt-3.5 text-[15px] leading-relaxed text-ink-soft max-w-[300px] text-pretty">
                    Melde dich an, um deine Tagesberichte fortzuschreiben und den Wochenbericht abzuschließen.
                </p>
            </div>

            <x-auth.week-preview :filled="4" />
        </div>

        {{-- Formular --}}
        <div class="p-9 flex flex-col justify-center gap-5">
            <div class="flex justify-between items-center gap-4">
                <h2 class="font-display text-[21px]">Anmelden</h2>
                <x-back-link />
            </div>

            @if (session('status'))
                <div class="border border-signed bg-signed-soft text-signed rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('email'))
                <div class="border border-stamp bg-stamp-soft text-stamp rounded-md px-3.5 py-3 text-[13.5px] leading-normal">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                <x-form.field
                    name="email"
                    label="E-Mail"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-baseline gap-3">
                        <x-form.label for="password">Passwort</x-form.label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-[12.5px] text-ink-soft underline decoration-rule underline-offset-[3px] hover:text-ink">
                                Passwort vergessen?
                            </a>
                        @endif
                    </div>

                    <x-form.input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        :invalid="$errors->has('password')"
                    />

                    <x-form.error :messages="$errors->get('password')" />
                </div>

                <x-form.checkbox name="remember">Angemeldet bleiben</x-form.checkbox>

                <x-button>Anmelden</x-button>
            </form>

            @if (Route::has('register'))
                <div class="border-t border-rule pt-4 flex flex-wrap justify-between items-center gap-3.5">
                    <span class="text-[13.5px] text-ink-soft">Noch kein Zugang?</span>
                    <x-button variant="secondary" :href="route('register')" class="px-4 py-2.5 text-[14px]">
                        Als Azubi registrieren
                    </x-button>
                </div>
            @endif

            <p class="text-[12.5px] text-ink-soft">Ausbilder-Zugänge richtet dein Betrieb ein.</p>
        </div>
    </div>

</x-layouts.auth>
