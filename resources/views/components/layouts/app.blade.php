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
  <title>{{ $title ? $title . ' — Mein Berichtsheft' : 'Mein Berichtsheft' }}</title>

  <script>
    (function () {
      let g = localStorage.getItem('theme');
      let d = g === 'dark' || (!g && window.matchMedia('(prefers-color-scheme: dark)').matches);
      document.documentElement.classList.toggle('dark', d);
      document.documentElement.style.colorScheme = d ? 'dark' : 'light';
    })();
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-paper-raised text-ink antialiased min-h-screen">

  <x-app.topbar :context="\$context ?? null" />

  <main class="max-w-[1280px] mx-auto px-5 pt-[22px] pb-15 flex flex-col">
    {{ \$slot }}
  </main>
</body>
</html>
