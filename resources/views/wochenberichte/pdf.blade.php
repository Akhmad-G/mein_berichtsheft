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

    .signature-grid {
      width: 100%;
      margin-top: 30px;
    }

    .signature-box {
      width: 48%;
      display: inline-block;
      vertical-align: top;
      border-top: 1px solid #111827;
      padding-top: 8px;
      margin-right: 2%;
      min-height: 110px;
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
    <p><strong>Berichtsnummer:</strong> {{ $report['berichtsnummer'] ?? '—' }}</p>
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
        <th style="width: 15%;">Tag</th>
        <th>Tätigkeiten</th>
        <th>Gelernt</th>
        <th>Probleme / Ereignisse</th>
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
            <td class="whitespace">{{ $report['tage'][$tag]['taetigkeiten'] ?? '' }}</td>
            <td class="whitespace">{{ $report['tage'][$tag]['gelernt'] ?? '' }}</td>
            <td class="whitespace">{{ $report['tage'][$tag]['probleme'] ?? '' }}</td>
          </tr>
        @endif
      @endforeach
    </tbody>
  </table>
  
  <div class="signature-grid">
    <div class="signature-box">
      <strong>Unterschrift Azubi</strong>
      
      @if (! empty($report['unterschriften']['azubi']))
        <p class="muted">
          {{ $report['unterschriften']['azubi']['name'] }}
          am {{ $report['unterschriften']['azubi']['signed_at'] }}
        </p>
        <img src="{{ $report['unterschriften']['azubi']['image'] }}" alt="Unterschrift Azubi">
      @else
        <p class="muted">Noch nicht unterschrieben.</p>
      @endif
    </div>
    
    <div class="signature-box">
      <strong>Unterschrift Ausbilder</strong>
      
      @if (! empty($report['unterschriften']['ausbilder']))
        <p class="muted">
          {{ $report['unterschriften']['ausbilder']['name'] }}
          am {{ $report['unterschriften']['ausbilder']['signed_at'] }}
        </p>
        <img src="{{ $report['unterschriften']['ausbilder']['image'] }}" alt="Unterschrift Ausbilder">
      @else
        <p class="muted">Noch nicht unterschrieben.</p>
      @endif
    </div>
  </div>
</body>
</html>