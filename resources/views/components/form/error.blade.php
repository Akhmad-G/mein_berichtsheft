@props(['messages' => [], 'hint' => null])

{{-- feste Zeilenhöhe: eine auftauchende Fehlermeldung verschiebt das Layout nicht --}}
<p class="min-h-[17px] text-[12.5px] leading-[17px] {{ $messages ? 'text-stamp' : 'text-ink-soft' }}">
    {{ $messages ? (is_array($messages) ? $messages[0] : $messages) : $hint }}
</p>
