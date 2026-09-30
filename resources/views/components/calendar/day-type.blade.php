@props([
    'day',                // App\Data\DayReport — the day of the sheet
    'count'    => 0,      // days of this type in the month
    'disabled' => false,  // true for Ausbilder or a submitted week
])

@use('App\Enums\DayType')

{{--
  Radios name="type" belong to the surrounding <form>.
  Work day → fields in [data-day-type-work] (in the page view) are shown,
  any other type → the absence block below.
  JS: resources/js/app.js → [data-day-type].
--}}
@php
  // classes written out in full so Tailwind finds them
  $options = [
      'work'     => ['dot' => 'bg-report-soft', 'on' => 'has-checked:bg-[color-mix(in_oklab,var(--color-report-soft)_75%,var(--color-paper))]'],
      'school'   => ['dot' => 'bg-school-soft',  'on' => 'has-checked:bg-[color-mix(in_oklab,var(--color-school-soft)_75%,var(--color-paper))]'],
      'vacation' => ['dot' => 'bg-vacation-soft',  'on' => 'has-checked:bg-[color-mix(in_oklab,var(--color-vacation-soft)_75%,var(--color-paper))]'],
      'sick'     => ['dot' => 'bg-sick-soft',   'on' => 'has-checked:bg-[color-mix(in_oklab,var(--color-sick-soft)_75%,var(--color-paper))]'],
      'holiday'  => ['dot' => 'bg-holiday-soft',    'on' => 'has-checked:bg-[color-mix(in_oklab,var(--color-holiday-soft)_75%,var(--color-paper))]'],
  ];
  $type   = DayType::tryFrom(old('type', $day->type->value)) ?? DayType::Work;
  $isSick = $type === DayType::Sick;
  $from   = old('from', $day->date->toDateString());
  $to     = old('to', $day->date->toDateString());
@endphp

<div class="flex flex-col gap-2"
     data-day-type
>
  <span class="text-[12.5px] text-ink-soft">Tagesart</span>

  <div class="flex flex-wrap gap-[7px]"
       role="radiogroup"
       aria-label="Tagesart"
  >
    @foreach (DayType::cases() as $option)
      <label data-day-type-chip
             data-label="{{ $option->label() }}"
        @class([
            'inline-flex items-center gap-2 text-[12.5px] px-[13px] py-[7px] rounded-full border',
            'border-rule bg-transparent text-ink-soft',
            'has-checked:border-ink has-checked:text-ink',
            'has-focus-visible:ring-1 has-focus-visible:ring-ink',
            $options[$option->value]['on'],
            $disabled ? 'cursor-default' : 'cursor-pointer hover:bg-paper-line',
        ])
      >
        <input type="radio"
               name="type"
               value="{{ $option->value }}"
               @checked($type === $option)
               @disabled($disabled)
               class="sr-only"
        >
        <span class="w-[9px] h-[9px] rounded-[2px] border border-rule shrink-0 {{ $options[$option->value]['dot'] }}"></span>
        {{ $option->label() }}
      </label>
    @endforeach
  </div>

  <p class="text-[12px] leading-normal text-ink-soft">
    {{ $disabled
        ? 'Die Tagesart kann hier nicht geändert werden.'
        : 'Urlaub, Krank und Feiertage trägst du hier ein — für mehrere Tage über „Von / Bis“.' }}
  </p>
  <x-form.error :messages="$errors->get('type')" />
</div>

<div data-day-type-absence
     @if ($type->isWork()) hidden @endif
     class="border border-rule rounded-lg px-4 py-[15px] flex flex-col gap-[13px]"
>
  <div class="flex flex-wrap items-baseline justify-between gap-3">
    <span class="font-display text-[16px]"
          data-day-type-title
    >{{ $type->label() }}</span>
    @if (! $type->isWork())
      <span class="text-[12px] text-ink-soft">{{ $count }} {{ $count === 1 ? 'Tag' : 'Tage' }} im {{ $day->date->isoFormat('MMMM') }}</span>
    @endif
  </div>

  <div class="grid grid-cols-[repeat(auto-fit,minmax(130px,1fr))] gap-3 items-end">
    <div class="flex flex-col gap-[7px]">
      <x-form.label for="from">Von</x-form.label>
      <x-form.input id="from"
                    name="from"
                    type="date"
                    :value="$from"
                    :invalid="$errors->has('from')"
                    :readonly="$disabled"
                    data-day-type-from
      />
    </div>
    <div class="flex flex-col gap-[7px]">
      <x-form.label for="to">Bis</x-form.label>
      <x-form.input id="to"
                    name="to"
                    type="date"
                    :value="$to"
                    :invalid="$errors->has('to')"
                    :readonly="$disabled"
                    data-day-type-to
      />
    </div>
    @unless ($disabled)
      <x-button variant="secondary"
                size="sm"
                name="range"
                value="1"
                class="whitespace-nowrap"
      >
        Für Zeitraum eintragen
      </x-button>
    @endunless
  </div>
  <x-form.error :messages="array_merge($errors->get('from'), $errors->get('to'))" />

  <div class="flex flex-col gap-[7px]">
    <x-form.label for="note"
                  data-day-type-note-label
    >
      {{ $isSick ? 'Nachweis / Bemerkung' : 'Bemerkung' }}
    </x-form.label>
    <x-form.input id="note"
                  name="note"
                  :value="old('note', $day->note)"
                  maxlength="200"
                  :placeholder="$isSick ? 'z. B. AU bis 04.09. eingereicht' : 'optional'"
                  :readonly="$disabled"
                  data-day-type-note
    />
  </div>
</div>
