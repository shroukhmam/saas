<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} — {{ tenant('name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink text-sm bg-ground">
<div class="min-h-screen flex flex-col items-center justify-center gap-6 px-4 py-10">
    <div class="flex flex-col items-center gap-3 text-center">
        <span class="w-12 h-12 rounded-xl bg-teal-700 text-white flex items-center justify-center font-extrabold text-xl">{{ mb_strtoupper(mb_substr(tenant('name'), 0, 1)) }}</span>
        <div class="flex flex-col gap-0.5">
            <span class="text-xl font-extrabold">{{ tenant('name') }}</span>
            <span class="font-mono text-[11px] tracking-wider text-muted">STORE ADMIN</span>
        </div>
    </div>

    <div class="w-full max-w-sm bg-white border border-line rounded-xl p-6">
        {{ $slot }}
    </div>
</div>
</body>
</html>
