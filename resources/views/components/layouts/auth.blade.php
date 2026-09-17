@props(['title' => null])

  <!DOCTYPE html>
<html lang="de"
      class="scroll-smooth"
>
<head>
  <meta charset="utf-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1"
  >
  <title>{{ $title ? $title . ' — Mein Tagesbericht' : 'Mein Tagesbericht' }}</title>

  {{-- Theme vor dem ersten Paint setzen: kein Aufblitzen der falschen Farbe --}}
  <script>
    (function () {
      var g = localStorage.getItem('theme');
      var d = g === 'dark' || (!g && window.matchMedia('(prefers-color-scheme: dark)').matches);
      document.documentElement.classList.toggle('dark', d);
      document.documentElement.style.colorScheme = d ? 'dark' : 'light';
    })();
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-paper-raised text-ink antialiased">
  <main class="max-w-5xl mx-auto px-4 sm:px-8 py-9 sm:py-12">
    {{ $slot }}
  </main>
</body>
</html>