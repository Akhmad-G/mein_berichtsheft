@props(['days' => [], 'selected' => null, 'note' => null])

{{--
  $days: Collection<App\Data\DayReport> Mon–Fri.
  Buttons stay at 42px: the strip fits the 64px header and the dividing lines of both pages stay level.
--}}
<x-notebook.page-header>
  <x-slot:lead>
    <span class="flex items-center gap-[5px] min-w-0">
      @foreach ($days as $day)
        @php $on = $selected && $day->date->isSameDay($selected); @endphp

        <a href="{{ route('calendar', ['day' => $day->date->toDateString()]) }}" @class([
          'w-11 h-[42px] box-border flex flex-col items-center justify-center leading-[1.15]',
          'border rounded-md',
          $on
            ? 'bg-ink text-paper border-ink'
            : ($day->calendarState() === 'report'
                ? 'border-rule bg-[color-mix(in_oklab,var(--color-report-soft)_75%,var(--color-paper))]'
                : 'border-rule bg-transparent'),
        ])>
          <span class="text-[10px] tracking-[.06em] uppercase opacity-70">{{ $day->date->isoFormat('dd') }}</span>
          <span class="text-[14px]">{{ $day->date->day }}</span>
        </a>
    @endforeach
    </span>
  </x-slot:lead>

  <x-slot:actions>
    <span class="text-[12.5px] text-ink-soft min-w-0 truncate">{{ $note }}</span>
  </x-slot:actions>
</x-notebook.page-header>
