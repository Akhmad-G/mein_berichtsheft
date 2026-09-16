@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

@php $id = $attributes->get('id') ?? $name; @endphp

<div class="flex flex-col gap-1.5">
    <x-form.label :for="$id">{{ $label }}</x-form.label>

    <x-form.input
        :id="$id"
        :name="$name"
        :type="$type"
        :value="old($name, $value)"
        :invalid="$errors->has($name)"
        {{ $attributes->except(['id', 'class']) }}
    />

    <x-form.error :messages="$errors->get($name)" :hint="$hint" />
</div>
