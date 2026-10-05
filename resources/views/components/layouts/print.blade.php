@props(['title' => null])

{{-- Print sheet: always light, A4. Opens the print dialog on load — "Als PDF speichern" there. --}}
  <!DOCTYPE html>
<html lang="de"
      style="color-scheme: light"
>
<head>
  <meta charset="utf-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1"
  >
  <title>{{ $title ? $title . ' — Mein Berichtsheft' : 'Mein Berichtsheft' }}</title>
  @vite(['resources/css/app.css'])
  <style>
    @page {
      size: A4;
      margin: 16mm;
    }
  </style>
</head>
<body class="font-sans bg-paper text-ink antialiased">

  <div class="print:hidden border-b border-rule bg-paper-line">
    <div class="max-w-[190mm] mx-auto px-5 py-3 flex items-center justify-between gap-3">
      <x-button variant="secondary"
                size="sm"
                :href="url()->previous()"
      >Zurück
      </x-button>
      <x-button size="sm"
                onclick="window.print()"
      >Drucken / PDF
      </x-button>
    </div>
  </div>

  <main class="max-w-[190mm] mx-auto px-5 py-8 print:p-0 flex flex-col gap-10">
    {{ $slot }}
  </main>

  <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
