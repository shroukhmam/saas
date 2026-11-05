<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}Central Admin — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink text-sm bg-ground">
<div class="min-h-screen flex flex-col lg:flex-row">

    @include('central.layouts.sidebar')

    <main class="flex-1 min-w-0 px-4 py-8 sm:px-8 lg:px-10 flex flex-col gap-6">

        {{-- Flash messages --}}
        @if (session('status'))
            <div role="status" class="rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-teal-900">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Optional page heading (used by Breeze pages such as Profile) --}}
        @isset($header)
            <header>{{ $header }}</header>
        @endisset

        {{ $slot }}
    </main>
</div>
</body>
</html>
