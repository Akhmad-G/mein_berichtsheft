@props(['name', 'checked' => false])

<label for="{{ $name }}" class="flex items-start gap-2.5 text-[13px] leading-relaxed text-ink-soft cursor-pointer">
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="checkbox"
        value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'w-[15px] h-[15px] mt-0.5 shrink-0 rounded border-rule accent-ink']) }}
    >
    <span>{{ $slot }}</span>
</label>
