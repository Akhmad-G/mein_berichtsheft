<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Mein Tagesbericht') }}</title>
        
        <link rel="icon" href="{{ asset('favicon-light.ico') }}" media="(prefers-color-scheme: light)">
        <link rel="icon" href="{{ asset('favicon-dark.ico') }}" media="(prefers-color-scheme: dark)">
        
        <script>
            (function () {
                let stored = localStorage.getItem('theme');
                let prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-paper text-ink antialiased">

        {{-- ================= NAV ================= --}}
        <header class="not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="max-w-5xl mx-auto flex items-center justify-between px-8 py-6">
                    <div class="flex items-center gap-2.5">
                        <svg viewBox="0 0 120 120" class="w-7 h-7" xmlns="http://www.w3.org/2000/svg">
                            <rect x="10" y="38" width="16" height="44" rx="6" class="fill-ink"/>
                            <rect x="30" y="38" width="16" height="44" rx="6" class="fill-ink"/>
                            <rect x="50" y="38" width="16" height="44" rx="6" class="fill-ink"/>
                            <rect x="70" y="38" width="16" height="44" rx="6" class="fill-ink"/>
                            <rect x="90" y="38" width="16" height="44" rx="6" class="fill-stamp"/>
                            <path d="M93.5 60 L97 64.5 L103 51" fill="none" class="stroke-paper" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="font-display text-lg font-medium">{{ config('app.name', 'Mein Tagesbericht') }}</span>
                    </div>

                    <div class="flex gap-3 items-center">
                        <x-theme-toggle/>

                        @auth
                            @if (Auth::user()->ausbildung_info_completed_at)
                                <a href="{{ url('/dashboard') }}"
                                   class="text-sm font-medium px-4 py-2 rounded-md bg-ink text-paper">Zum Dashboard</a>
                            @else
                                @if (Route::has('ausbildung-info.create'))
                                    <a href="{{ route('ausbildung-info.create') }}"
                                       class="text-sm font-medium px-4 py-2 rounded-md bg-ink text-paper">Registrierung fortsetzen</a>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium px-4 py-2 rounded-md border border-rule">Anmelden</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="text-sm font-medium px-4 py-2 rounded-md bg-ink text-paper">Als Azubi registrieren</a>
                            @endif
                        @endauth
                    </div>
                </nav>
            @endif
        </header>

        {{-- ================= HERO ================= --}}
        <header class="max-w-5xl mx-auto px-8 pt-12 pb-20 grid grid-cols-1 md:grid-cols-2 gap-14 items-center">
            <div>
                <div class="w-9 h-0.5 bg-stamp mb-5"></div>
                <h1 class="font-display text-4xl md:text-[44px] leading-[1.14] max-w-md">
                    Der Ausbildungsnachweis, den du nicht am Freitagabend zusammensuchen musst.
                </h1>
                <p class="mt-5 text-[17px] leading-relaxed text-ink-soft max-w-sm">
                    Tagesberichte schreibst du, wann sie passieren — jederzeit bearbeitbar oder löschbar.
                    Dein Wochenbericht übernimmt sie automatisch. Was nicht passt, kannst du direkt dort ändern
                    – oder den Wochenbericht komplett von Grund auf neu schreiben, bevor er unterschrieben wird.
                </p>
                <div class="flex gap-3.5 mt-8">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-3 rounded-md bg-ink text-paper text-[15px] font-medium">Als Azubi starten</a>
                    @endif
                    <a href="#rollen" class="px-5 py-3 rounded-md border border-rule text-[15px] font-medium">Für Ausbilder ansehen</a>
                </div>
                <p class="mt-4 text-[13.5px] text-ink-soft">
                    Registrierung aktuell für Azubis. Ausbilder-Zugänge richtet dein Betrieb ein.
                </p>
            </div>

            {{-- Wochenbericht-Vorschau --}}
            <div class="bg-paper-line border border-rule rounded-xl px-6 pt-6 pb-6 max-w-sm md:ml-auto shadow-[0_18px_40px_-20px_rgba(32,38,44,0.25)]">
                <div class="flex justify-between items-baseline mb-4">
                    <span class="font-display text-[17px]">KW 36</span>
                    <span class="text-[13px] text-ink-soft">09.–13. Juni</span>
                </div>

                @foreach (['Mo','Di','Mi','Do','Fr'] as $i => $tag)
                    <div class="flex items-center gap-3 py-2 border-t border-rule">
                        <span class="w-8 text-[13.5px] text-ink-soft shrink-0">{{ $tag }}</span>
                        <span class="flex-1 h-px bg-rule"></span>
                        <span class="w-[18px] h-[18px] rounded-full bg-signed-soft flex items-center justify-center shrink-0 animate-pop-in"
                              style="animation-delay: {{ $i * 150 }}ms">
                            <svg viewBox="0 0 10 10" class="w-2.5 h-2.5">
                                <path d="M1 5 L4 8 L9 1" fill="none" stroke="#3F5D42" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                @endforeach

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-rule">
                    <svg viewBox="0 0 96 34" class="w-24 h-[34px] text-ink">
                        <path d="M4 26 Q10 8 16 22 Q20 30 26 18 Q30 8 34 20 Q40 32 48 14 Q52 6 58 18 Q64 28 72 12 Q78 4 84 16"
                              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span class="flex items-center gap-1.5 bg-stamp-soft text-stamp text-[12.5px] font-medium px-2.5 py-1.5 rounded-full">
                        <svg viewBox="0 0 12 12" class="w-3 h-3"><path d="M1 6 L4.5 9.5 L11 2" fill="none" stroke="#8B3A2B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Unterschrieben
                    </span>
                </div>
            </div>
        </header>

        {{-- ================= ABLAUF ================= --}}
        <section id="ablauf" class="border-t border-rule py-16">
            <div class="max-w-5xl mx-auto px-8">
                <h2 class="font-display text-[30px] max-w-md">Vier Schritte, jede Woche dieselben.</h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-9 mt-11">
                    <div>
                        <div class="font-display text-[15px] text-stamp mb-3">1</div>
                        <h3 class="text-[17px] font-medium mb-2">Profil &amp; Ausbildungsdaten</h3>
                        <p class="text-[14px] text-ink-soft leading-relaxed">Ausbildungsberuf, Betrieb, Abteilung und Beginn hinterlegst du einmal bei der Registrierung.</p>
                    </div>
                    <div>
                        <div class="font-display text-[15px] text-stamp mb-3">2</div>
                        <h3 class="text-[17px] font-medium mb-2">Tagesberichte schreiben</h3>
                        <p class="text-[14px] text-ink-soft leading-relaxed">Für jeden Tag deiner Ausbildung — jederzeit bearbeitbar oder löschbar, solange die Woche offen ist.</p>
                    </div>
                    <div>
                        <div class="font-display text-[15px] text-stamp mb-3">3</div>
                        <h3 class="text-[17px] font-medium mb-2">Wochenbericht erstellen</h3>
                        <p class="text-[14px] text-ink-soft leading-relaxed">Wird aus deinen Tagesberichten zusammengestellt — automatisch befüllt, aber frei von dir anpassbar.</p>
                    </div>
                    <div>
                        <div class="font-display text-[15px] text-stamp mb-3">4</div>
                        <h3 class="text-[17px] font-medium mb-2">Unterschreiben</h3>
                        <p class="text-[14px] text-ink-soft leading-relaxed">Du unterschreibst deine Zeile, dein Ausbilder seine. Danach ist der Bericht gesperrt.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= ROLLEN ================= --}}
        <section id="rollen" class="py-6 pb-16">
            <div class="max-w-5xl mx-auto px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 border border-rule rounded-xl overflow-hidden">
                    <div class="bg-paper-line border-b md:border-b-0 md:border-r border-rule p-9">
                        <div class="text-[13px] text-ink-soft mb-2.5">Für Azubis</div>
                        <h3 class="font-display text-[22px] mb-3">Schreib, wenn es passiert ist.</h3>
                        <ul class="mt-4 space-y-2.5">
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Profil und Ausbildungsdaten selbst anlegen
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Tagesberichte schreiben, bearbeiten, löschen
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Wochenbericht aus den eigenen Einträgen erstellen und anpassen
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Eigene Zeile unterschreiben — nicht die des Ausbilders
                            </li>
                        </ul>
                    </div>
                    <div class="p-9">
                        <div class="text-[13px] text-ink-soft mb-2.5">Für Ausbilder</div>
                        <h3 class="font-display text-[22px] mb-3">Alle Berichte deiner Azubis, an einem Ort.</h3>
                        <ul class="mt-4 space-y-2.5">
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Aktuell vom Betrieb eingerichtet, Azubis fest zugeordnet
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Wochenberichte der zugeordneten Azubis einsehen
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Eigene Zeile unterschreiben, ohne Einträge zu verändern
                            </li>
                            <li class="flex gap-2.5 text-[14.5px] text-ink-soft leading-relaxed">
                                <x-icon-check /> Kein Zugriff auf Berichte fremder Azubis
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FUNKTIONEN (Ledger) ================= --}}
        <section id="funktionen" class="py-4 pb-16">
            <div class="max-w-5xl mx-auto px-8">
                <h2 class="font-display text-[26px] mb-8 max-w-md">Was im Hintergrund mitläuft.</h2>

                <div class="border-t border-rule">
                    <div class="flex justify-between items-baseline gap-6 py-5 border-b border-rule">
                        <div class="text-[16px] font-medium max-w-xs">Rollenbasierte Rechte</div>
                        <div class="text-[14px] text-ink-soft text-right max-w-sm">Azubi schreibt und bearbeitet, Ausbilder sieht und unterschreibt — keiner unterschreibt für den anderen.</div>
                    </div>
                    <div class="flex justify-between items-baseline gap-6 py-5 border-b border-rule">
                        <div class="text-[16px] font-medium max-w-xs">Gesperrt nach Unterschrift</div>
                        <div class="text-[14px] text-ink-soft text-right max-w-sm">Ein unterschriebener Wochenbericht kann nicht mehr bearbeitet oder gelöscht werden.</div>
                    </div>
                    <div class="flex justify-between items-baseline gap-6 py-5 border-b border-rule">
                        <div class="text-[16px] font-medium max-w-xs">PDF-Export</div>
                        <div class="text-[14px] text-ink-soft text-right max-w-sm">Wochenbericht als PDF, wahlweise mit oder ohne die eingeholten Unterschriften.</div>
                    </div>
                    <div class="flex justify-between items-baseline gap-6 py-5 border-b border-rule">
                        <div class="text-[16px] font-medium max-w-xs">Eigenes GitLab als Speicher</div>
                        <div class="text-[14px] text-ink-soft text-right max-w-sm">Jede Version wird abgelegt und ist wiederherstellbar — die Daten bleiben in eurer eigenen Infrastruktur.</div>
                    </div>
                </div>

                <p class="text-[13px] text-ink-soft mt-6">
                    Geplant: Rollenwahl bei der Registrierung, direkte Azubi-Ausbilder-Zuordnung, Kalenderansicht für Wochen und Monate, Speicherung zusätzlich als Markdown.
                </p>
            </div>
        </section>

        {{-- ================= CTA ================= --}}
        <section class="bg-ink text-paper py-16 mt-5">
            <div class="max-w-5xl mx-auto px-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <h2 class="font-display text-[28px] max-w-md">Diese Woche noch anfangen, statt sie am Freitag aufzuarbeiten.</h2>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-5 py-3 rounded-md bg-paper text-ink text-[15px] font-medium shrink-0">Als Azubi starten</a>
                @endif
            </div>
        </section>

        <footer class="max-w-5xl mx-auto px-8 py-8 flex justify-between text-[13px] text-ink-soft">
            <span>{{ config('app.name', 'Mein Tagesbericht') }}</span>
            <span>Für Ausbildungsbetriebe in Deutschland</span>
        </footer>
        
    </body>
</html>
