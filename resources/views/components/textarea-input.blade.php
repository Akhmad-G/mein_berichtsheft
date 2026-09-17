@props([
    'disabled' => false,
    'readonly' => false,
])

<textarea
    {{ $disabled ? 'disabled' : '' }}
  {{ $readonly ? 'readonly' : '' }}
  {!! $attributes->merge([
      'class' => 'mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
  ]) !!}
>{{ $slot }}</textarea>
