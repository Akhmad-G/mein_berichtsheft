@props(['status' => 'offen', 'size' => 'sm'])

@php
  $tones = [
      'signiert' => 'text-bericht bg-bericht-soft border-bericht',
      'wartet'   => 'text-stamp bg-stamp-soft border-stamp',
      'offen'    => 'text-ink-soft bg-transparent border-rule',
      'schule'   => 'text-schule bg-schule-soft border-schule',
  ];
  $tone = $tones[$status] ?? $tones['offen'];
@endphp

<span @class([
    'inline-flex items-center border rounded-full whitespace-nowrap shrink-0 uppercase tracking-[.06em]',
    $tone,
    $size === 'md' ? 'text-[12px] px-[13px] py-[5px]' : 'text-[11px] px-[9px] py-[3px]',
])>{{ $slot }}</span>
