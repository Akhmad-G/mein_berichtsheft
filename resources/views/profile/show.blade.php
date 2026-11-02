<x-layouts.app title="Profil">

    {{-- no tabs: way back as its own button, folder rounded on all corners --}}
    <div class="flex pb-2.5">
        <x-button variant="secondary" size="sm" :href="route('home')" class="bg-paper-line">
            <svg viewBox="0 0 12 12" class="w-2.5 h-2.5"><path d="M7.5 1.5 L3 6 L7.5 10.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Zurück zum Heft
        </x-button>
    </div>

    @php $isAusbilder = auth()->user()->isAusbilder(); @endphp

    <x-notebook :tabs="false">
        {{-- left page: personal data --}}
        <x-slot:left>
            <x-notebook.page-header :title="$isAusbilder ? 'Mein Zugang' : 'Stammdaten'" tight />

            <div class="px-5 pt-1.5 pb-5 flex flex-col flex-1 justify-between">
                <div>
                    @if (session('status') === 'password-updated')
                        <div class="mb-4 border border-signed bg-signed-soft text-signed rounded-md px-3.5 py-2.5 text-[13px]">
                            Passwort wurde erfolgreich geändert.
                        </div>
                    @endif

                    @foreach ($details as $label => $value)
                        <div class="flex items-baseline justify-between gap-4 py-[11px] border-b border-rule">
                            <span class="text-[12.5px] text-ink-soft shrink-0">{{ $label }}</span>
                            <span class="text-[14px] text-right">{{ $value ?: '—' }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Action buttons at the bottom of the section --}}
                <div class="mt-6 pt-4 border-t border-rule/50 flex flex-wrap items-center justify-between gap-2">
                    <div data-modal>
                        <x-button type="button" variant="secondary" size="sm" data-modal-open>
                            Passwort ändern
                        </x-button>

                        <div data-modal-backdrop
                             @if (! $errors->updatePassword->isNotEmpty()) hidden @endif
                             class="fixed inset-0 z-50 flex items-center justify-center bg-ink/35 px-4"
                        >
                            <div class="w-full max-w-[480px] rounded-xl border border-rule bg-paper shadow-xl">
                                <div class="flex items-start justify-between gap-4 border-b border-rule px-5 py-4">
                                    <div>
                                        <h3 class="font-display text-[19px] text-ink">Passwort ändern</h3>
                                        <p class="mt-1 text-[12.5px] text-ink-soft">
                                            Wähle ein sicheres, mindestens 8 Zeichen langes Passwort.
                                        </p>
                                    </div>

                                    <button type="button"
                                            data-modal-close
                                            class="text-[22px] leading-none text-ink-soft hover:text-ink"
                                            aria-label="Schließen"
                                    >
                                        ×
                                    </button>
                                </div>

                                <form method="POST"
                                      action="{{ route('password.update') }}"
                                      class="px-5 py-5 flex flex-col gap-4"
                                >
                                    @csrf
                                    @method('put')

                                    <div>
                                        <x-form.label for="update_current_password" required>Aktuelles Passwort</x-form.label>
                                        <x-form.input id="update_current_password"
                                                      name="current_password"
                                                      type="password"
                                                      required
                                                      autocomplete="current-password"
                                                      class="mt-1.5"
                                                      placeholder="••••••••"
                                        />
                                        @if ($errors->updatePassword->has('current_password'))
                                            <p class="mt-1.5 text-[12.5px] text-stamp">{{ $errors->updatePassword->first('current_password') }}</p>
                                        @endif
                                    </div>

                                    <div>
                                        <x-form.label for="update_password" required>Neues Passwort</x-form.label>
                                        <x-form.input id="update_password"
                                                      name="password"
                                                      type="password"
                                                      required
                                                      autocomplete="new-password"
                                                      class="mt-1.5"
                                                      placeholder="Mindestens 8 Zeichen"
                                        />
                                        @if ($errors->updatePassword->has('password'))
                                            <p class="mt-1.5 text-[12.5px] text-stamp">{{ $errors->updatePassword->first('password') }}</p>
                                        @endif
                                    </div>

                                    <div>
                                        <x-form.label for="update_password_confirmation" required>Neues Passwort bestätigen</x-form.label>
                                        <x-form.input id="update_password_confirmation"
                                                      name="password_confirmation"
                                                      type="password"
                                                      required
                                                      autocomplete="new-password"
                                                      class="mt-1.5"
                                                      placeholder="Passwort wiederholen"
                                        />
                                        @if ($errors->updatePassword->has('password_confirmation'))
                                            <p class="mt-1.5 text-[12.5px] text-stamp">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center justify-end gap-2">
                                        <x-button type="button"
                                                  variant="secondary"
                                                  size="sm"
                                                  data-modal-close
                                        >
                                            Abbrechen
                                        </x-button>

                                        <x-button type="submit"
                                                  variant="primary"
                                                  size="sm"
                                        >
                                            Passwort speichern
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div data-modal>
                        <x-button type="button" variant="secondary" size="sm" class="text-stamp hover:text-stamp" data-modal-open>
                            Profil löschen
                        </x-button>

                        <div data-modal-backdrop
                             @if (! $errors->userDeletion->isNotEmpty()) hidden @endif
                             class="fixed inset-0 z-50 flex items-center justify-center bg-ink/35 px-4"
                        >
                            <div class="w-full max-w-[480px] rounded-xl border border-rule bg-paper shadow-xl">
                                <div class="flex items-start justify-between gap-4 border-b border-rule px-5 py-4">
                                    <div>
                                        <h3 class="font-display text-[19px] text-stamp">Profil löschen</h3>
                                        <p class="mt-1 text-[12.5px] text-ink-soft">
                                            Bist du sicher, dass du deinen Account löschen möchtest? Alle Daten werden unwiderruflich gelöscht.
                                        </p>
                                    </div>

                                    <button type="button"
                                            data-modal-close
                                            class="text-[22px] leading-none text-ink-soft hover:text-ink"
                                            aria-label="Schließen"
                                    >
                                        ×
                                    </button>
                                </div>

                                <form method="POST"
                                      action="{{ route('profile.destroy') }}"
                                      class="px-5 py-5 flex flex-col gap-4"
                                >
                                    @csrf
                                    @method('delete')

                                    <div>
                                        <x-form.label for="delete_password" required>Passwort zur Bestätigung</x-form.label>
                                        <x-form.input id="delete_password"
                                                      name="password"
                                                      type="password"
                                                      required
                                                      autocomplete="current-password"
                                                      class="mt-1.5"
                                                      placeholder="••••••••"
                                        />
                                        @if ($errors->userDeletion->has('password'))
                                            <p class="mt-1.5 text-[12.5px] text-stamp">{{ $errors->userDeletion->first('password') }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex items-center justify-end gap-2">
                                        <x-button type="button"
                                                  variant="secondary"
                                                  size="sm"
                                                  data-modal-close
                                        >
                                            Abbrechen
                                        </x-button>

                                        <x-button type="submit"
                                                  variant="danger"
                                                  size="sm"
                                        >
                                            Profil unwiderruflich löschen
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:left>

        {{-- right page: cover sheet of the training record --}}
        <x-slot:right>
            @if ($azubi)
                <x-notebook.page-header>
                    <x-slot:lead>
                        @if ($isAusbilder)
                            {{-- switch the record right here, without leaving the profile --}}
                            <form method="GET" action="{{ route('profile.show') }}" class="flex items-center gap-[7px] min-w-0">
                                <label for="azubi" class="text-[12px] tracking-[.06em] uppercase text-ink-soft whitespace-nowrap">Nachweis</label>
                                <select id="azubi" name="azubi" onchange="this.form.submit()"
                                        class="text-[13.5px] px-2.5 py-[7px] border border-rule rounded-md bg-paper
                                               min-w-0 cursor-pointer focus:outline-none focus:border-ink focus:ring-1 focus:ring-ink">
                                    @foreach ($azubis as $a)
                                        <option value="{{ $a->id }}" @selected($azubi->is($a))>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <h2 class="font-display text-[18px] min-w-0 truncate">Deckblatt</h2>
                        @endif
                    </x-slot:lead>

                    <x-slot:actions>
                        <x-button variant="secondary" size="sm" :href="route('training-record.print', $azubi)">Nachweis drucken / PDF</x-button>
                    </x-slot:actions>
                </x-notebook.page-header>

                <div class="px-[22px] py-[22px] flex flex-col gap-4 flex-1">
                    <x-training-record.cover :azubi="$azubi" :signed-weeks="$signedWeeks" />
                </div>
            @else
                <x-notebook.page-header title="Deckblatt" />
                <p class="px-[22px] py-[22px] text-[13.5px] text-ink-soft">Dir sind noch keine Azubis zugeordnet.</p>
            @endif
        </x-slot:right>
    </x-notebook>

</x-layouts.app>
