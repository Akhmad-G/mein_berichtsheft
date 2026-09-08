<div {{ $attributes->merge([
    'class' => 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 shadow sm:rounded-lg',
]) }}>
    {{ $slot }}
</div>