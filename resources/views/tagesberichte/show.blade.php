<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Tagesbericht — {{ $report['date'] ?? '' }}
    </h2>
  </x-slot>
  
  <div class="py-8 max-w-3xl mx-auto space-y-4">
    <div>
      <span class="text-gray-900 dark:text-gray-100">Wochentag</span>
      <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $report['wochentag'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-gray-900 dark:text-gray-100">Ausbildungsjahr</span>
      <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $report['ausbildungsjahr'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-gray-900 dark:text-gray-100">Ausbildungswoche</span>
      <p class="mb-4 text-gray-700 dark:text-gray-300">{{ $report['ausbildungswoche'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-gray-900 dark:text-gray-100">Tätigkeiten</span>
      <p class="mb-4 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['taetigkeiten'] ?? '—' }}</p>
    </div>
    
    @if (! empty($report['gelernt']))
      <div>
        <span class="text-gray-900 dark:text-gray-100">Gelernt</span>
        <p class="mb-4 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['gelernt'] }}</p>
      </div>
    @endif
    
    @if (! empty($report['probleme']))
      <div>
        <span class="text-gray-900 dark:text-gray-100">Probleme</span>
        <p class="mb-4 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['probleme'] }}</p>
      </div>
    @endif
    
    <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-sm text-gray-900 dark:text-gray-100">
      ← Zurück zum Dashboard
    </a>
  </div>
</x-app-layout>