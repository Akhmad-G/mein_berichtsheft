<x-layouts.auth title="Als Azubi registrieren">

    <div class="border border-rule rounded-xl overflow-hidden">

        {{-- Kopfzeile --}}
        <div class="bg-paper-line border-b border-rule px-6 sm:px-8 py-5 flex flex-wrap justify-between items-center gap-4">
            <x-brand :size="24" :text="16" />

            <div class="flex items-center gap-5">
                <x-back-link :href="url('/')" label="Zurück" />
                <x-button variant="secondary" :href="route('login')" class="px-3.5 py-2 text-[13.5px]">
                    Anmelden
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[minmax(0,0.72fr)_minmax(0,1.28fr)]">

            {{-- Papierseite --}}
            <div class="bg-paper-line border-b md:border-b-0 md:border-r border-rule px-7 py-8 flex flex-col gap-6">
                <div>
                    <div class="w-9 h-0.5 bg-stamp mb-4"></div>
                    <h1 class="font-display text-[26px] leading-[1.2] text-pretty">Einmal anlegen, dann nur noch schreiben.</h1>
                    <p class="mt-3 text-[14.5px] leading-relaxed text-ink-soft text-pretty">
                        Deine Ausbildungsdaten stehen später auf jedem Wochenbericht — du gibst sie nur hier ein.
                    </p>
                </div>

                <div class="border-t border-rule">
                    @foreach ([
                        'Registrierung ist für Azubis — kostenlos und ohne Einladung',
                        'Deine Tagesberichte sieht nur dein zugeordneter Ausbilder',
                        'Unterschriebene Wochenberichte bleiben unverändert erhalten',
                        'Jede Version liegt im GitLab deines Betriebs',
                    ] as $punkt)
                        <div class="flex gap-2.5 items-start py-2.5 border-b border-rule text-[13.5px] leading-snug text-ink-soft">
                            <x-report-check />
                            <span>{{ $punkt }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Formular: Zugang + Ausbildungsdaten in einem Schritt --}}
            <form method="POST" action="{{ route('register') }}" class="px-7 py-8 flex flex-col gap-6">
                @csrf

                <x-form.section step="1" title="Zugangsdaten">
                    <x-form.field name="vorname"  label="Vorname"  required autofocus autocomplete="given-name" placeholder="Lena" />
                    <x-form.field name="nachname" label="Nachname" required autocomplete="family-name" placeholder="Hartmann" />
                    <x-form.field name="email" label="E-Mail" type="email" required autocomplete="username" placeholder="lena.hartmann@betrieb.de" />
                    <x-form.field name="password" label="Passwort" type="password" required autocomplete="new-password" placeholder="Mindestens 8 Zeichen" />
                    <x-form.field name="password_confirmation" label="Passwort bestätigen" type="password" required autocomplete="new-password" placeholder="Passwort wiederholen" />
                </x-form.section>

                <x-form.section step="2" title="Ausbildungsdaten">
                    <x-form.field name="ausbildungsberuf"   label="Ausbildungsberuf"   required placeholder="Fachinformatikerin AE" />
                    <x-form.field name="ausbildungsbetrieb" label="Ausbildungsbetrieb" required placeholder="Nordwerk GmbH" />
                    <x-form.field name="abteilung"          label="Abteilung"          placeholder="Anwendungsentwicklung" />
                    <x-form.field name="ausbildungsbeginn"  label="Ausbildungsbeginn"  type="date" required />

                    <x-slot:note>Alles davon lässt sich später im Profil ändern.</x-slot:note>
                </x-form.section>

                <div class="border-t border-rule pt-4.5 flex flex-wrap items-center justify-between gap-4">
                    <x-form.checkbox name="gitlab_einverstanden" class="max-w-[330px]">
                        Ich bin einverstanden, dass meine Berichte im GitLab meines Betriebs gespeichert werden.
                    </x-form.checkbox>

                    <x-button>Registrierung abschließen</x-button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.auth>
