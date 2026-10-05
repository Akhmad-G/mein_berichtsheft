@props(['week']) {{-- App\Data\WeeklyReport with days --}}

{{-- Weekly report sheet — used on the right page, in print and in the training record --}}
@php
    $steps = collect(config('reports.learning_steps', []))->pluck('label', 'id');
@endphp

<div {{ $attributes->class('flex flex-col gap-4 flex-1') }}>
    <div class="flex flex-wrap items-start justify-between gap-[18px] border-b border-rule pb-3.5">
        <div>
            <h2 class="font-display text-[20px]">
                Wochenbericht KW {{ $week->week }}
                @if ($week->number)
                    <span class="text-ink-soft text-[15px]">· Nr. {{ $week->number }}</span>
                @endif
            </h2>
            <p class="mt-1.5 text-[12.5px] text-ink-soft">
                {{ collect([$week->azubi->name, $week->azubi->ausbildungsbetrieb, $week->azubi->abteilung])->filter()->join(' · ') }}
            </p>
        </div>
        <x-stamp :status="$week->status->stamp()" size="md">{{ $week->status->label() }}</x-stamp>
    </div>

    <div class="flex flex-col">
        @foreach ($week->days as $day)
            <div class="grid grid-cols-[78px_minmax(0,1fr)_auto] gap-4 items-start py-3 border-b border-rule">
                <div class="flex flex-col gap-0.5">
                    <span class="text-[13px]">{{ $day->date->isoFormat('dddd') }}</span>
                    <span class="text-[11.5px] text-ink-soft">{{ $day->date->format('d.m.Y') }}</span>
                </div>

                <div class="min-w-0 flex flex-col gap-[5px]">
                    @if (! $day->type->isWork())
                        <span class="flex flex-wrap items-center gap-2">
                            <x-stamp :status="$day->stamp()">{{ $day->type->label() }}</x-stamp>
                            @if ($day->note)
                                <span class="text-[12.5px] text-ink-soft">{{ $day->note }}</span>
                            @endif
                        </span>
                    @else
                        <span @class(['text-[13.5px] leading-snug whitespace-pre-line', ! $day->hasContent() && 'text-ink-soft italic'])>{{ $day->activities ?: 'noch nicht erfasst' }}</span>

                        @if ($day->learningSteps)
                            <span class="text-[11.5px] text-ink-soft">
                                {{ collect($day->learningSteps)->map(fn ($id) => isset($steps[$id]) ? "{$id} · {$steps[$id]}" : $id)->join(', ') }}
                            </span>
                        @endif
                    @endif
                </div>

                <span class="text-[12.5px] text-ink-soft whitespace-nowrap">{{ $day->duration ?: '—' }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-auto grid grid-cols-1 sm:grid-cols-2 gap-[18px] border-t border-rule pt-[18px]">
        <x-weekly-report.signature role="Auszubildende/r" :name="$week->azubi->name" :date="$week->submittedAt" />
        <x-weekly-report.signature role="Ausbilder/in" :name="$week->signedByName" :date="$week->signedAt" />
    </div>
</div>
