@props(['schritt', 'checked' => false, 'disabled' => false])

<label @class([
    'inline-flex items-center gap-[7px] text-[12.5px] px-3 py-1.5 rounded-full border border-rule text-left',
    $checked ? 'bg-bericht-soft text-ink' : 'bg-transparent text-ink-soft',
    $disabled ? 'cursor-default' : 'cursor-pointer hover:bg-paper-line',
])>
    <input
        type="checkbox"
        name="learningSteps[]"
        value="{{ $schritt->id }}"
        @checked($checked)
        @disabled($disabled)
        class="sr-only"
    >
    <span class="font-mono text-[11px] opacity-85">{{ $schritt->referenz }}</span>
    <span>{{ $schritt->titel }}</span>
</label>
