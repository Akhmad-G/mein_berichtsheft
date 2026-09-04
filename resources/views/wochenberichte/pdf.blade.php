<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title>Wochenbericht</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #111827;
    }

    h1 {
      font-size: 20px;
      margin-bottom: 4px;
    }

    h2 {
      font-size: 15px;
      margin-top: 20px;
      margin-bottom: 8px;
      border-bottom: 1px solid #d1d5db;
      padding-bottom: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }

    th,
    td {
      border: 1px solid #d1d5db;
      padding: 8px;
      vertical-align: top;
    }

    th {
      background: #f3f4f6;
      text-align: left;
    }

    .meta {
      margin-bottom: 16px;
    }

    .meta p {
      margin: 2px 0;
    }

    .signature-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }

    .signature-table th {
      background: #f3f4f6;
      text-align: left;
      padding: 8px;
      border: 1px solid #d1d5db;
    }

    .signature-cell {
      width: 50%;
      height: 120px;
      padding: 8px;
      border: 1px solid #d1d5db;
      vertical-align: top;
    }

    .signature-cell img {
      max-width: 220px;
      max-height: 75px;
      display: block;
      margin-top: 8px;
      margin-bottom: 6px;
    }

    .signature-box img {
      max-width: 220px;
      max-height: 80px;
      display: block;
      margin-top: 8px;
    }

    .muted {
      color: #6b7280;
      font-size: 11px;
    }

    .whitespace {
      white-space: pre-line;
    }
  </style>
</head>
<body>
  <h1>Wochenbericht</h1>
  
  <div class="meta">
    <p><strong>Kalenderwoche:</strong> {{ $report['kalenderwoche'] ?? '—' }}</p>
    <p><strong>Zeitraum:</strong> {{ $report['week_start'] ?? '—' }} bis {{ $report['week_end'] ?? '—' }}</p>
    <p><strong>Name:</strong> {{ $report['user']['name'] ?? $owner->name }}</p>
    <p><strong>Ausbildungsberuf:</strong> {{ $report['user']['ausbildungsberuf'] ?? '—' }}</p>
    <p><strong>Ausbildungsbetrieb:</strong> {{ $report['user']['ausbildungsbetrieb'] ?? '—' }}</p>
  </div>
  
  <h2>Bericht</h2>
  
  <table>
    <thead>
      <tr>
        <th style="width: 18%;">Tag</th>
        <th style="width: 82%;">Wochenübersicht</th>
      </tr>
    </thead>
    <tbody>
      @foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'] as $tag)
        @if (! empty($report['tage'][$tag]))
          <tr>
            <td>
              <strong>{{ $tag }}</strong><br>
              <span class="muted">{{ $report['tage'][$tag]['date'] ?? '' }}</span>
            </td>
            <td class="whitespace">
              @if (! empty($report['tage'][$tag]['taetigkeiten']))
                {{ $report['tage'][$tag]['taetigkeiten'] }}
              @endif
              
              @if (! empty($report['tage'][$tag]['gelernt']))
                {{ "\n\n" }}{{ $report['tage'][$tag]['gelernt'] }}
              @endif
              
              @if (! empty($report['tage'][$tag]['probleme']))
                {{ "\n\nBesondere Ereignisse / Probleme:\n" }}{{ $report['tage'][$tag]['probleme'] }}
              @endif
              
              @if (
                empty($report['tage'][$tag]['taetigkeiten'])
                && empty($report['tage'][$tag]['gelernt'])
                && empty($report['tage'][$tag]['probleme'])
              )
                —
              @endif
            </td>
          </tr>
        @endif
      @endforeach
    </tbody>
  </table>
  
  @php
    $azubiSignatureDate = ! empty($report['unterschriften']['azubi']['signed_at'])
        ? substr($report['unterschriften']['azubi']['signed_at'], 0, 10)
        : null;

    $ausbilderSignatureDate = ! empty($report['unterschriften']['ausbilder']['signed_at'])
        ? substr($report['unterschriften']['ausbilder']['signed_at'], 0, 10)
        : null;
  @endphp
  
  <table class="signature-table">
    <thead>
      <tr>
        <th style="width: 50%;">Unterschrift Azubi</th>
        <th style="width: 50%;">Unterschrift Ausbilder</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="signature-cell">
          @if (! empty($report['unterschriften']['azubi']))
            <img src="{{ $report['unterschriften']['azubi']['image'] }}" alt="Unterschrift Azubi">
            <p class="muted">
              {{ $report['unterschriften']['azubi']['name'] }}, {{ $azubiSignatureDate }}
            </p>
          @else
            <p class="muted">Noch nicht unterschrieben.</p>
          @endif
        </td>
        
        <td class="signature-cell">
          @if (! empty($report['unterschriften']['ausbilder']))
            <img src="{{ $report['unterschriften']['ausbilder']['image'] }}" alt="Unterschrift Ausbilder">
            <p class="muted">
              {{ $report['unterschriften']['ausbilder']['name'] }}, {{ $ausbilderSignatureDate }}
            </p>
          @else
            <p class="muted">Noch nicht unterschrieben.</p>
          @endif
        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>