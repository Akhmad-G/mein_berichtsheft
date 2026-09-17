@props([
    'title',
])

<section {{ $attributes->merge(['class' => 'space-y-3']) }}>
  <div class="flex items-center gap-3">
    @isset($icon)
      <div class="text-gray-500 dark:text-gray-400">
        {{ $icon }}
      </div>
    @endisset

    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
      {{ $title }}
    </h3>
  </div>

  <div>
    {{ $slot }}
  </div>
</section>