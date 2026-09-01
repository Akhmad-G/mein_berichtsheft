<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">
      Tagesbericht — {{ $report['date'] ?? '' }}
    </h2>
  </x-slot>
  
  <div class="py-8 max-w-3xl mx-auto space-y-4">
    <div>
      <span class="text-sm text-gray-400">Wochentag</span>
      <p>{{ $report['wochentag'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-sm text-gray-400">Ausbildungsjahr</span>
      <p>{{ $report['ausbildungsjahr'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-sm text-gray-400">Ausbildungswoche</span>
      <p>{{ $report['ausbildungswoche'] ?? '—' }}</p>
    </div>
    
    <div>
      <span class="text-sm text-gray-400">Tätigkeiten</span>
      <p class="whitespace-pre-line">{{ $report['taetigkeiten'] ?? '—' }}</p>
    </div>
    
    @if (! empty($report['gelernt']))
      <div>
        <span class="text-sm text-gray-400">Gelernt</span>
        <p class="whitespace-pre-line">{{ $report['gelernt'] }}</p>
      </div>
    @endif
    
    @if (! empty($report['probleme']))
      <div>
        <span class="text-sm text-gray-400">Probleme</span>
        <p class="whitespace-pre-line">{{ $report['probleme'] }}</p>
      </div>
    @endif
    
    <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-sm text-gray-400 hover:text-white">
      ← Zurück zum Dashboard
    </a>
  </div>
</x-app-layout>