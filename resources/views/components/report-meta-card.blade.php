@props([
    'title' => 'Informationen',
    'items' => [],
    'open' => true,
])

<details
    {{ $open ? 'open' : '' }}
    {{ $attributes->merge([
        'class' => 'group rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/40',
    ]) }}
>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 marker:hidden">
        <div class="flex items-center gap-3">
            @isset($icon)
                <span class="text-gray-500 dark:text-gray-400">
                    {{ $icon }}
                </span>
            @endisset
            
            <span class="text-base font-semibold text-gray-900 dark:text-gray-100">
                {{ $title }}
            </span>
        </div>
        
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 shrink-0 text-gray-500 transition-transform group-open:rotate-180 dark:text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </summary>
    
    <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-700">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-0">
            @foreach ($items as $item)
                <div class="sm:border-r sm:border-gray-200 sm:px-6 sm:first:pl-0 sm:last:border-r-0 sm:last:pr-0 dark:sm:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $item['label'] }}
                    </p>
                    
                    <p
                        @isset($item['id'])
                            id="{{ $item['id'] }}"
                        @endisset
                        class="mt-1 text-base font-semibold text-gray-900 dark:text-gray-100"
                    >
                        {{ $item['value'] ?? '—' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</details>