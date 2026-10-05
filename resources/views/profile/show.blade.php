<x-layouts.app title="Profil">

    {{-- no tabs: way back as its own button, folder rounded on all corners --}}
    <div class="flex pb-2.5">
        <x-button variant="secondary" size="sm" :href="route('home')">
            <svg viewBox="0 0 12 12" class="w-2.5 h-2.5"><path d="M7.5 1.5 L3 6 L7.5 10.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Zurück zum Heft
        </x-button>
    </div>

    @php $isAusbilder = auth()->user()->isAusbilder(); @endphp

    <x-notebook :tabs="false">
        {{-- left page: personal data --}}
        <x-slot:left>
            <x-notebook.page-header :title="$isAusbilder ? 'Mein Zugang' : 'Stammdaten'" tight />

            <div class="px-5 pt-1.5 pb-5 flex flex-col flex-1">
                @foreach ($details as $label => $value)
                    <div class="flex items-baseline justify-between gap-4 py-[11px] border-b border-rule">
                        <span class="text-[12.5px] text-ink-soft shrink-0">{{ $label }}</span>
                        <span class="text-[14px] text-right">{{ $value ?: '—' }}</span>
                    </div>
                @endforeach

{{--                <div class="mt-[18px] flex flex-wrap gap-[9px]">--}}
{{--                    <x-button variant="secondary" size="sm" :href="route('profile.edit')">Daten ändern</x-button>--}}
{{--                    <x-button variant="secondary" size="sm" :href="route('profile.edit') . '#password'">Passwort ändern</x-button>--}}
{{--                </div>--}}
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
