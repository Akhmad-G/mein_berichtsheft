<x-layouts.app title="Wochenberichte">

  <x-heft.tabs current="weeks"
               :items="$tabs"
  />

  <x-heft>
    {{-- linke Seite: alle Kalenderwochen --}}
    <x-slot:left>
      <x-heft.page-header title="Alle Kalenderwochen"
                          tight
      >
        <x-slot:actions>
          <x-heft.stepper :prev="route('wochenberichte.index', ['year' => $year - 1])"
                          :next="route('wochenberichte.index', ['year' => $year + 1])"
                          :jump="route('wochenberichte.index')"
                          jump-label="Aktuelle KW"
          />
        </x-slot:actions>
      </x-heft.page-header>

      <div class="px-5 py-[11px] border-b border-rule flex flex-wrap gap-1.5">
        @foreach (['alle' => 'Alle', 'offen' => 'Offen', 'signiert' => 'Signiert'] as $key => $label)
          <x-heft.filter-chip :href="route('wochenberichte.index', ['filter' => $key])"
                              :active="$filter === $key"
          >{{ $label }}</x-heft.filter-chip>
        @endforeach
      </div>

      <div class="flex-1">
        @foreach ($weeks as $listedWeek)
          <x-heft.ledger-row :href="$listedWeek->path ? route('wochenberichte.show', ['path' => $listedWeek->path]) : null"
                             :active="$week->path === $listedWeek->path"
          >
            <span class="flex flex-col items-start gap-0.5 w-[52px] shrink-0">
                <span class="font-display text-[16px]">{{ $listedWeek->kw }}</span>
                <span class="text-[10.5px] tracking-[.06em] uppercase text-ink-soft">KW</span>
            </span>

            <span class="flex-1 min-w-0 flex flex-col items-start gap-[5px]">
                <span class="text-[13.5px]">{{ $listedWeek->period }}</span>
                <x-heft.day-dots :filled="$listedWeek->recordedDays" />
            </span>

            <x-stamp :status="$listedWeek->statusStamp">{{ $listedWeek->statusText }}</x-stamp>
          </x-heft.ledger-row>
        @endforeach
      </div>
    </x-slot:left>

    {{-- rechte Seite: Blatt zum Unterschreiben und Drucken --}}
    <x-slot:right>
      <x-heft.page-header>
        <x-slot:lead>
                    <span class="text-[12.5px] text-ink-soft min-w-0 truncate">
                        {{ $week->period }}
                      @if (auth()->user()->isAusbilder())
                        · {{ $week->azubi->name }}
                      @else
                        · {{ $week->recordedDays }} von 5 Tagen erfasst
                      @endif
                    </span>
        </x-slot:lead>

{{--        <x-slot:actions>--}}
{{--          <x-button variant="secondary"--}}
{{--                    size="sm"--}}
{{--                    :href="route('wochenbericht.drucken', $week)"--}}
{{--          >Drucken--}}
{{--          </x-button>--}}
{{--          @if ($week->path)--}}
{{--            <x-button variant="secondary"--}}
{{--                      size="sm"--}}
{{--                      :href="route('wochenberichte.pdf', ['path' => $week->path])"--}}
{{--            >PDF--}}
{{--            </x-button>--}}
{{--            <x-wochenbericht.sign-button :week="$week" />--}}
{{--          @endif--}}
{{--        </x-slot:actions>--}}
      </x-heft.page-header>

      <div class="px-[22px] py-[22px] flex flex-col gap-4 flex-1">
        <div class="flex flex-wrap items-start justify-between gap-[18px] border-b border-rule pb-3.5">
          <div>
            <h2 class="font-display text-[20px]">Wochenbericht KW {{ $week->kw }}</h2>
            <p class="mt-1.5 text-[12.5px] text-ink-soft">
              {{ $week->azubi->name }} · {{ $week->azubi->ausbildungsbetrieb }} · {{ $week->azubi->abteilung }}
            </p>
          </div>
          <x-stamp :status="$week->statusStamp"
                   size="md"
          >{{ $week->statusText }}</x-stamp>
        </div>

        <div class="flex flex-col">
          @foreach ($week->days as $day)
            <div class="grid grid-cols-[78px_minmax(0,1fr)_auto] gap-4 items-start py-3 border-b border-rule">
              <div class="flex flex-col gap-0.5">
                <span class="text-[13px]">{{ $day->datum->isoFormat('dddd') }}</span>
                <span class="text-[11.5px] text-ink-soft">{{ $day->datum->format('d.m.Y') }}</span>
              </div>

              <div class="min-w-0 flex flex-col gap-[5px]">
                <span @class(['text-[13.5px] leading-snug', ! $day->taetigkeiten && 'text-ink-soft italic'])>
                    {{ $day->taetigkeiten ?: 'noch nicht erfasst' }}
                </span>

                @if ($day->lernschritte->isNotEmpty())
                  <span class="text-[11.5px] text-ink-soft">
                    {{ $day->lernschritte->map(fn ($s) => $s->referenz . ' · ' . $s->titel)->join(', ') }}
                  </span>
                @endif
              </div>

              <span class="text-[12.5px] text-ink-soft whitespace-nowrap">{{ $day->dauer ?: '—' }}</span>
            </div>
          @endforeach
        </div>

        <div class="mt-auto grid grid-cols-1 sm:grid-cols-2 gap-[18px] border-t border-rule pt-[18px]">
          <x-wochenbericht.signatur rolle="Auszubildende"
                                    :name="$week->azubi->name"
                                    :date="$week->eingereicht_am"
          />
          <x-wochenbericht.signatur rolle="Ausbilder"
                                    :name="$week->ausbilder?->name"
                                    :date="$week->unterschrieben_am"
          />
        </div>
      </div>
    </x-slot:right>
  </x-heft>

</x-layouts.app>
