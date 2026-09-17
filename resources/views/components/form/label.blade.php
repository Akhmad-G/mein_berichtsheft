@props(['for' => null])

<label {{ $attributes->merge(['for' => $for, 'class' => 'text-[13px] text-ink-soft']) }}>
  {{ $slot }}
</label>
