@props([
    'step',               // ['id' => 'A-10', 'label' => '…'] from config/reports.php
    'checked' => false,
    'disabled' => false,
])

<label @class([
    'inline-flex items-center gap-[7px] text-[12.5px] px-3 py-1.5 rounded-full border border-rule text-left',
    'has-checked:bg-report-soft has-checked:text-ink bg-transparent text-ink-soft',
    'has-focus-visible:ring-1 has-focus-visible:ring-ink',
    $disabled ? 'cursor-default' : 'cursor-pointer hover:bg-paper-line',
])>
  <input type="checkbox"
         name="learning_steps[]"
         value="{{ $step['id'] }}"
         @checked($checked)
         @disabled($disabled)
         class="sr-only"
  >
  <span class="font-mono text-[11px] opacity-85">{{ $step['id'] }}</span>
  <span>{{ $step['label'] }}</span>
</label>
