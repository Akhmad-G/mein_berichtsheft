@props(['days' => [], 'selected' => null, 'note' => null])

<x-heft.page-header>
  <x-slot:lead>
        <span class="flex items-center gap-[5px] min-w-0">
            @foreach ($days as $day)
            @php
              $d  = $day['datum'];
              $on = $selected && $d->isSameDay($selected);
            @endphp
            <a href="{{ route('tagesbericht', ['datum' => $d->toDateString()]) }}" @class([
                    'w-11 h-[42px] box-border flex flex-col items-center justify-center leading-[1.15]',
                    'border rounded-md',
                    $on
                        ? 'bg-ink text-paper border-ink'
                        : ($day['art'] === 'bericht'
                            ? 'border-rule bg-[color-mix(in_oklab,var(--color-bericht-soft)_75%,var(--color-paper))]'
                            : 'border-rule bg-transparent'),
                ])>
                    <span class="text-[10px] tracking-[.06em] uppercase opacity-70">{{ $d->isoFormat('dd') }}</span>
                    <span class="text-[14px]">{{ $d->day }}</span>
                </a>
          @endforeach
        </span>
  </x-slot:lead>

  <x-slot:actions>
    <span class="text-[12.5px] text-ink-soft min-w-0 truncate">{{ $note }}</span>
  </x-slot:actions>
</x-heft.page-header>
