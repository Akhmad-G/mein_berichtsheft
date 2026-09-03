<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">
      Wochenbericht — {{ $report['week_label'] ?? '' }}
    </h2>
  </x-slot>
  
  <div class="py-8 max-w-3xl mx-auto space-y-6">
    @foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'] as $tag)
      @if (! empty($report['tage'][$tag]))
        <div>
          <span class="text-gray-900 dark:text-gray-100">{{ $tag }}</span>
          <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['taetigkeiten'] }}</p>
          <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['gelernt'] }}</p>
          <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['probleme'] }}</p>
        </div>
      @endif
    @endforeach
    
    <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-sm text-gray-900 dark:text-gray-100 hover:text-white">
      ← Zurück zum Dashboard
    </a>
  </div>
</x-app-layout>