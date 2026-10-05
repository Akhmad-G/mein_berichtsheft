@props(['azubi', 'signedWeeks' => 0])

{{-- Cover sheet of the Ausbildungsnachweis — profile page and print --}}
<div {{ $attributes->class('border border-rule rounded-lg bg-paper-line px-6 py-[26px] flex flex-col gap-5 flex-1') }}>
    <div>
        <span class="text-[11.5px] tracking-[.1em] uppercase text-ink-soft">Ausbildungsnachweis</span>
        <h3 class="font-display text-[24px] mt-2 leading-tight">{{ $azubi->name }}</h3>
    </div>

    <div class="flex flex-col">
        @foreach ([
            'Ausbildungsberuf' => $azubi->ausbildungsberuf,
            'Betrieb'          => $azubi->ausbildungsbetrieb,
            'Abteilung'        => $azubi->abteilung,
            'Zeitraum'         => $azubi->trainingPeriod(),
            'Ausbilder'        => $azubi->ausbilder?->name,
        ] as $label => $value)
            <div class="flex items-baseline justify-between gap-3.5 py-2.5 border-t border-rule">
                <span class="text-[12.5px] text-ink-soft">{{ $label }}</span>
                <span class="text-[13.5px] text-right">{{ $value ?: '—' }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-auto flex items-end justify-between gap-4">
        <span class="text-[12px] leading-snug text-ink-soft max-w-[230px]">
            Dieses Blatt steht vor jedem Ausdruck des Nachweises.
        </span>
        <x-stamp status="signed" size="md">{{ $signedWeeks }} Wochen unterschrieben</x-stamp>
    </div>
</div>
