<x-layouts.print :title="'Wochenbericht KW ' . $week->week . '/' . $week->year">
    <x-weekly-report.sheet :week="$week" />
</x-layouts.print>
