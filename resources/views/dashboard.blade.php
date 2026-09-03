<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 flex items-center gap-4">
                  <a href="{{ route('tagesberichte.create') }}">
                    <x-primary-button>{{ __('Neuer Tagesbericht') }}</x-primary-button>
                  </a>
                  <a href="{{ route('wochenberichte.create') }}">
                    <x-primary-button>{{ __('Neuer Wochenbericht') }}</x-primary-button>
                  </a>
                </div>
            </div>
            <div class="mt-4 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex items-center gap-4">
                  <h2 class="p-6 text-lg text-gray-900 dark:text-gray-100">Alle Berichtshefte</h2>
                </div>
                <div class="text-white">
                  @forelse ($reports as $report)
                    @php
                      $routeName = $report['type'] === 'wochenbericht'
                          ? 'wochenberichte.show'
                          : 'tagesberichte.show';
                    @endphp
                    
                    <a href="{{ route($routeName, ['path' => $report['encoded_path']]) }}"
                       class="flex justify-between items-center px-4 py-3 border-b border-gray-700 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                      <span>
                        {{ $report['name'] }}
                      </span>
                      <span class="text-xs uppercase tracking-wide text-gray-400">
                        {{ $report['type'] === 'wochenbericht' ? 'Wochenbericht' : 'Tagesbericht' }}
                      </span>
                    </a>
                  @empty
                    <p class="text-white">Noch keine Berichte vorhanden.</p>
                  @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
