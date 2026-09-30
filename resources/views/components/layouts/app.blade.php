@props(['title' => null, 'context' => null])

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

  {{-- set theme before first paint (no flash); toggling lives in resources/js/app.js --}}
  <script>
    (function () {
      let getTheme = localStorage.getItem('theme');
      let darkTheme = getTheme === 'dark' || (!getTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
      document.documentElement.classList.toggle('dark', darkTheme);
      document.documentElement.style.colorScheme = darkTheme ? 'dark' : 'light';
    })();
  </script>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-paper-raised text-ink antialiased min-h-screen">

  <x-app.topbar :context="$context" />

  <main class="max-w-[1280px] mx-auto px-5 pt-[22px] pb-15 flex flex-col">
    @if (session('status'))
      <p class="mb-3 text-[13px] text-signed" role="status">{{ session('status') }}</p>
    @endif

    {{ $slot }}
  </main>
</body>
</html>
