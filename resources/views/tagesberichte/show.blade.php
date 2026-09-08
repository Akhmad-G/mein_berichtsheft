<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tagesbericht — {{ $report['date'] ?? '' }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('tagesberichte.form', [
                        'action' => '#',
                        'method' => 'GET',
                        'report' => $report,
                        'readonly' => true,
                    ])
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('tagesberichte.edit', ['tagesberichte' => \App\Support\GitLabPath::encode($path)]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white text-black rounded hover:bg-gray-200"
                >
                    Bearbeiten
                </a>
                
                <form method="POST"
                      action="{{ route('tagesberichte.destroy', ['tagesberichte' => \App\Support\GitLabPath::encode($path)]) }}"
                      onsubmit="return confirm('Tagesbericht wirklich löschen?');">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Löschen
                    </button>
                </form>
                
                <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-sm text-gray-900 dark:text-gray-100">
                    ← Zurück zum Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>