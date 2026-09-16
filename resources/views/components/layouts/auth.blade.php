@props(['title' => null])

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' — Mein Tagesbericht' : 'Mein Tagesbericht' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-paper text-ink antialiased">
    <main class="max-w-5xl mx-auto px-4 sm:px-8 py-8 sm:py-12">
        {{ $slot }}
    </main>
</body>
</html>
