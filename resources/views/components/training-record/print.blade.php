{{-- Ausbildungsnachweis: cover sheet + every signed week, one per page --}}
<x-layouts.print :title="'Ausbildungsnachweis ' . $azubi->name">

    <x-training-record.cover :azubi="$azubi" :signed-weeks="$weeks->count()" class="min-h-[240mm]" />

    @foreach ($weeks as $week)
        <x-weekly-report.sheet :week="$week" class="break-before-page" />
    @endforeach

</x-layouts.print>
