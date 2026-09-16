@props(['current' => 'login'])

<div class="border-b border-rule bg-paper-line px-6 py-3.5 flex flex-wrap justify-between items-center gap-4">
    <x-brand :size="24" :text="16" />

    <div class="flex items-center gap-3">
        <x-theme-toggle />

        <x-back-link />

        @if ($current === 'login')
            <x-button variant="secondary" size="sm" :href="route('register')">Registrieren</x-button>
        @else
            <x-button variant="secondary" size="sm" :href="route('login')">Anmelden</x-button>
        @endif
    </div>
</div>
