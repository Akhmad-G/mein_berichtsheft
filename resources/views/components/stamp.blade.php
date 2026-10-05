@props(['status' => 'open', 'size' => 'sm'])

{{--
  Status stamp.
  Day:  recorded | open | school | vacation | sick | holiday   (DayType::stamp())
  Week: open | pending | signed                              (WeekStatus::stamp())
--}}
@php
    $tones = [
        'recorded' => 'text-report bg-report-soft border-report',
        'signed'   => 'text-report bg-report-soft border-report',
        'pending'  => 'text-stamp bg-stamp-soft border-stamp',
        'open'     => 'text-ink-soft bg-transparent border-rule',
        'school'   => 'text-school bg-school-soft border-school',
        'vacation' => 'text-vacation bg-vacation-soft border-vacation',
        'sick'     => 'text-sick bg-sick-soft border-sick',
        'holiday'  => 'text-holiday bg-holiday-soft border-holiday',
    ];
    $tone = $tones[$status] ?? $tones['open'];
@endphp

<span @class([
    'inline-flex items-center border rounded-full whitespace-nowrap shrink-0 uppercase tracking-[.06em]',
    $tone,
    $size === 'md' ? 'text-[12px] px-[13px] py-[5px]' : 'text-[11px] px-[9px] py-[3px]',
])>{{ $slot }}</span>
