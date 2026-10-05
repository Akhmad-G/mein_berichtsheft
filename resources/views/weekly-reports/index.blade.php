<x-layouts.app title="Wochenberichte">

    <x-notebook.tabs current="weekly-reports" :items="$tabs" />

    @php $isAusbilder = auth()->user()->isAusbilder(); @endphp

    <x-notebook>
        {{-- left page: all calendar weeks --}}
        <x-slot:left>
            <x-notebook.page-header :title="'Kalenderwochen ' . $year" tight>
                <x-slot:actions>
                    <x-notebook.stepper
                        :prev="route('weekly-reports.index', ['year' => $year - 1, 'filter' => $filter])"
                        :next="route('weekly-reports.index', ['year' => $year + 1, 'filter' => $filter])"
                        :jump="route('weekly-reports.index')"
                        jump-label="Aktuelle KW"
                    />
                </x-slot:actions>
            </x-notebook.page-header>

            <div class="px-5 py-[11px] border-b border-rule flex flex-wrap gap-1.5">
                @foreach (['all' => 'Alle', 'open' => 'Offen', 'signed' => 'Signiert'] as $key => $label)
                    <x-notebook.filter-chip
                        :href="route('weekly-reports.index', ['year' => $year, 'filter' => $key])"
                        :active="$filter === $key"
                    >{{ $label }}</x-notebook.filter-chip>
                @endforeach
            </div>

            <div class="flex-1">
                @forelse ($weeks as $w)
                    <x-notebook.ledger-row :href="route('weekly-reports.show', $w->routeParams())" :active="$w->is($week)">
                        <span class="flex flex-col items-start gap-0.5 w-[52px] shrink-0">
                            <span class="font-display text-[16px]">{{ $w->week }}</span>
                            <span class="text-[10.5px] tracking-[.06em] uppercase text-ink-soft">KW</span>
                        </span>

                        <span class="flex-1 min-w-0 flex flex-col items-start gap-[5px]">
                            <span class="text-[13.5px] truncate max-w-full">
                                {{ $isAusbilder ? $w->azubi->name . ' · ' : '' }}{{ $w->period() }}
                            </span>
                            <x-notebook.day-dots :filled="$w->recordedDays" />
                        </span>

                        <x-stamp :status="$w->status->stamp()">{{ $w->status->label() }}</x-stamp>
                    </x-notebook.ledger-row>
                @empty
                    <p class="px-5 py-6 text-[13px] text-ink-soft">
                        {{ $isAusbilder ? 'Keine eingereichten Wochen.' : 'Keine Wochen in diesem Filter.' }}
                    </p>
                @endforelse
            </div>
        </x-slot:left>

        {{-- right page: sheet to sign and print --}}
        <x-slot:right>
            @if ($week)
                <x-notebook.page-header>
                    <x-slot:lead>
                        <span class="text-[12.5px] text-ink-soft min-w-0 truncate">
                            {{ $week->period() }} ·
                            {{ $isAusbilder ? $week->azubi->name : $week->recordedDays . ' von 5 Tagen erfasst' }}
                        </span>
                    </x-slot:lead>

                    <x-slot:actions>
                        <x-button variant="secondary" size="sm" :href="route('weekly-reports.print', $week->routeParams())">Drucken / PDF</x-button>
                        <x-weekly-report.sign-button :week="$week" />
                    </x-slot:actions>
                </x-notebook.page-header>

                <x-weekly-report.sheet :week="$week" class="px-[22px] py-[22px]" />
                <div class="px-[22px] pb-4"><x-form.error :messages="$errors->get('week')" /></div>
            @else
                <x-notebook.page-header title="Kein Wochenbericht" />
                <p class="px-[22px] py-[22px] text-[13.5px] text-ink-soft">
                    {{ $isAusbilder
                        ? 'Sobald ein Azubi eine Woche einreicht, erscheint sie hier.'
                        : 'Schreib deinen ersten Tagesbericht im Kalender.' }}
                </p>
            @endif
        </x-slot:right>
    </x-notebook>

</x-layouts.app>
