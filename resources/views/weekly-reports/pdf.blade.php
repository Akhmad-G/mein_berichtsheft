@php
    /** @var \App\Data\WeeklyReport $week */
    $azubi = $week->azubi;
    $days  = $week->days->sortKeys()->values();
    $hours = $days->sum(fn ($d) => $d->type->isWork() ? (float) str_replace(',', '.', $d->duration ?? 0) : 0);
    $fmt   = fn ($v) => rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',');
    $lines = fn (?string $t) => collect(preg_split('/\R/', (string) $t))->map(fn ($l) => trim($l))->filter()->values();
    $font  = fn (string $f) => resource_path('fonts/' . $f);

    $signatureImage = function (?array $signature): ?string {
        if (! $signature || empty($signature['paths'])) {
            return null;
        }

        $paths = collect($signature['paths'])
            ->map(fn ($path) => '<path d="' . e($path) . '" fill="none" stroke="#20262C" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>')
            ->implode('');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="900" height="260" viewBox="' . e($signature['viewBox'] ?? '0 0 900 260') . '">' . $paths . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    };

@endphp
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<title>Wochenbericht KW {{ $week->week }}/{{ $week->year }}</title>
<style>
    @font-face { font-family: 'Plex'; font-weight: normal; src: url('{{ $font('IBMPlexSans-Regular.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Plex'; font-weight: bold;   src: url('{{ $font('IBMPlexSans-SemiBold.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Fraunces'; font-weight: normal; src: url('{{ $font('Fraunces-Medium.ttf') }}') format('truetype'); }
    @font-face { font-family: 'PlexMono'; font-weight: normal; src: url('{{ $font('IBMPlexMono-Regular.ttf') }}') format('truetype'); }
    @font-face { font-family: 'Caveat'; font-weight: normal; src: url('{{ $font('Caveat-Medium.ttf') }}') format('truetype'); }

    @page { size: A4; margin: 13mm 14mm 20mm; }
    body { margin: 0; font-family: 'Plex', 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1.45; color: #20262C; }
    table { border-collapse: collapse; width: 100%; }
    td, th { vertical-align: top; text-align: left; padding: 0; }

    .display { font-family: 'Fraunces', 'DejaVu Serif', serif; }
    .mono    { font-family: 'PlexMono', 'DejaVu Sans Mono', monospace; }
    .soft    { color: #5B6470; }
    .cap     { font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; color: #5B6470; font-weight: normal; }
    .right   { text-align: right; }

    .head td { border-bottom: 2px solid #20262C; padding-bottom: 10px; vertical-align: bottom; }

    .box { border: 1px solid #CBB994; border-radius: 5px; }
    .meta td { padding: 7px 10px; border-right: 1px solid #CBB994; border-bottom: 1px solid #CBB994; }
    .meta td.last  { border-right: 0; }
    .meta tr.last td { border-bottom: 0; }
    .meta .v { font-size: 11.5px; font-weight: bold; margin-top: 2px; }

    .days th { background: #ECE2CC; padding: 7px 9px; border-left: 1px solid #CBB994; }
    .days td { padding: 9px; border-left: 1px solid #CBB994; border-top: 1px solid #CBB994; }
    .days th.first, .days td.first { border-left: 0; }
    .days tr { page-break-inside: avoid; }
    .days .total td { border-top: 1px solid #20262C; font-weight: bold; padding: 7px 9px; }
    .bullet td { border: 0 !important; padding: 0 0 2px 0 !important; }
    .tag { border: 1px solid #8B3A2B; color: #8B3A2B; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; padding: 1px 6px; border-radius: 3px; }

    .signs { margin-top: 22px; page-break-inside: avoid; }
    .sign { border: 1px solid #CBB994; border-radius: 5px; padding: 10px 12px; }
    .sign.ok { border-color: #3F5D42; background: #E1E8DC; }
    .sign.ok .cap, .sign.ok .by { color: #3F5D42; }
    .sign.ok .by { border-top-color: #3F5D42; }
    .hand { height: 58px; margin: 2px 0 4px; }
    .hand img { height: 58px; width: 201px; display: block; }
    .by { font-size: 9.5px; color: #5B6470; border-top: 1px solid #CBB994; padding-top: 5px; padding-bottom: 3px; min-height: 28px; }

    .foot { position: fixed; left: 0; right: 0; bottom: -12mm; font-size: 8.5px; color: #5B6470; }
    .pagenum:after { content: "Seite " counter(page) " / " counter(pages); }
</style>
</head>
<body>

<div class="foot">
    <table><tr>
        <td>Der Ausbilder bestätigt mit seiner Unterschrift die Richtigkeit und Vollständigkeit der Angaben. Erstellt mit Mein Berichtsheft am {{ now()->format('d.m.Y') }}</td>
        <td class="right pagenum" style="width:70px;vertical-align:bottom"></td>
    </tr></table>
</div>

<table class="head"><tr>
    <td class="display" style="font-size:24px">Wochenbericht</td>
    <td class="right" style="width:120px">
        <span class="soft" style="font-size:11px">Nr.</span>
        <span class="mono" style="font-size:20px">{{ str_pad($week->number ?? $week->week, 3, '0', STR_PAD_LEFT) }}</span>
    </td>
</tr></table>

<div class="box" style="margin-top:14px">
    <table class="meta">
        <tr>
            <td style="width:37%"><div class="cap">Auszubildende/r</div><div class="v">{{ $azubi->name }}</div></td>
            <td style="width:37%"><div class="cap">Ausbildungsberuf</div><div class="v">{{ $azubi->ausbildungsberuf }}</div></td>
            <td class="last"><div class="cap">Kalenderwoche</div><div class="v">KW {{ $week->week }} / {{ $week->year }}</div></td>
        </tr>
        <tr class="last">
            <td><div class="cap">Ausbildungsbetrieb</div><div class="v">{{ $azubi->ausbildungsbetrieb }}</div></td>
            <td><div class="cap">Abteilung</div><div class="v">{{ $azubi->abteilung }}</div></td>
            <td class="last"><div class="cap">Zeitraum</div><div class="v">{{ $week->period() }}</div></td>
        </tr>
    </table>
</div>

<div class="box" style="margin-top:16px">
    <table class="days">
        <thead>
            <tr>
                <th class="cap first" style="width:78px;color:#20262C">Tag</th>
                <th class="cap" style="color:#20262C">Tätigkeiten</th>
                <th class="cap" style="width:52px;color:#20262C">LF</th>
                <th class="cap right" style="width:34px;color:#20262C">Std.</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($days as $day)
            <tr>
                <td class="first">
                    <div style="font-weight:bold">{{ $day->date->locale('de')->isoFormat('dddd') }}</div>
                    <div class="mono soft" style="font-size:9.5px">{{ $day->date->format('d.m.Y') }}</div>
                </td>
                <td>
                    @if ($day->type->isWork())
                        @php $l = $lines($day->activities); @endphp
                        @if ($l->count() > 1 && ! str_starts_with($l->first(), '-'))
                            <div style="margin-bottom:3px">{{ $l->shift() }}</div>
                        @endif
                        @if ($l->isEmpty())
                            <span class="soft">—</span>
                        @else
                            <table class="bullet">
                                @foreach ($l as $line)
                                    <tr><td style="width:10px">•</td><td>{{ ltrim($line, '-•* ') }}</td></tr>
                                @endforeach
                            </table>
                        @endif
                    @else
                        <span class="tag">{{ $day->type->label() }}</span>
                        @if ($day->note)<span class="soft">&nbsp; {{ $day->note }}</span>@endif
                    @endif
                </td>
                <td class="mono soft" style="font-size:9.5px">
                    {{ $day->type->isWork() && $day->learningSteps ? implode(', ', $day->learningSteps) : '—' }}
                </td>
                <td class="right">{{ $day->type->isWork() && $day->duration ? $day->duration : '—' }}</td>
            </tr>
        @endforeach
            <tr class="total">
                <td class="first right" colspan="3" style="border-left:0">Wochenstunden gesamt</td>
                <td class="right" style="border-left:0">{{ $fmt($hours) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<table class="signs"><tr>
    <td style="width:50%;padding-right:9px;">
        <div class="sign {{ $week->submittedAt ? 'ok' : '' }}">
            <div class="cap">Auszubildende/r</div>
            <div class="hand">
                @if ($image = $signatureImage($week->submittedSignature))
                  <img src="{{ $image }}" alt="">
                @endif
{{--              @if ($week->submittedSignature)--}}
{{--                <svg viewBox="{{ $week->submittedSignature['viewBox'] ?? '0 0 900 260' }}">--}}
{{--                  {!! $signatureSvg($week->submittedSignature) !!}--}}
{{--                </svg>--}}
{{--              @endif--}}
            </div>
            <div class="by">
                @if ($week->submittedAt)
                    Digital unterschrieben am {{ $week->submittedAt->format('d.m.Y') }}<br>von {{ $azubi->name }}
                @else
                    Noch nicht unterschrieben
                @endif
            </div>
        </div>
    </td>
    <td style="width:50%;padding-left:9px">
        <div class="sign {{ $week->isSigned() ? 'ok' : '' }}">
            <div class="cap">Ausbilder/in</div>
            <div class="hand">
                @if ($image = $signatureImage($week->signedSignature))
                  <img src="{{ $image }}" alt="">
                @endif
{{--              @if ($week->signedSignature)--}}
{{--                <svg viewBox="{{ $week->signedSignature['viewBox'] ?? '0 0 900 260' }}">--}}
{{--                  {!! $signatureSvg($week->signedSignature) !!}--}}
{{--                </svg>--}}
{{--              @endif--}}
            </div>
            <div class="by">
                @if ($week->isSigned())
                    Digital unterschrieben am {{ $week->signedAt?->format('d.m.Y') }}<br>von {{ $week->signedByName }}
                @else
                    Noch nicht unterschrieben
                @endif
            </div>
        </div>
    </td>
</tr></table>

</body>
</html>
