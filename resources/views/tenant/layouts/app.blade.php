@php
    $user = Auth::guard('tenant')->user();
    $items = [
        ['label' => 'Dashboard', 'route' => 'tenant.dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'],
        ['label' => 'Products', 'route' => null, 'icon' => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>'],
        ['label' => 'Orders', 'route' => null, 'icon' => '<path d="M6 2l-2 4v14h16V6l-2-4z"/><path d="M4 6h16"/><path d="M16 10a4 4 0 01-8 0"/>'],
        ['label' => 'Customers', 'route' => null, 'icon' => '<circle cx="9" cy="8" r="4"/><path d="M2 21c1-4 4-6 7-6s6 2 7 6"/><path d="M17 4a4 4 0 010 8M22 21c-.5-2.5-2-4.5-4-5.5"/>'],
        ['label' => 'Settings', 'route' => null, 'icon' => '<path d="M4 6h10M4 12h4M12 12h8M4 18h12"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ tenant('name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-ink text-sm bg-ground">
<div class="min-h-screen flex flex-col lg:flex-row">
    <aside class="bg-ink text-slate-300 lg:w-60 lg:min-h-screen shrink-0 px-4 py-6 flex flex-col gap-7">
        <div class="flex items-center gap-2.5 px-2 min-w-0">
            <span class="w-[34px] h-[34px] shrink-0 rounded-[9px] bg-teal-500 text-ink flex items-center justify-center font-extrabold text-base">{{ mb_strtoupper(mb_substr(tenant('name'), 0, 1)) }}</span>
            <div class="flex flex-col min-w-0">
                <span class="text-white font-bold text-[15px] truncate">{{ tenant('name') }}</span>
                <span class="font-mono text-[11px] tracking-wider text-slate-400">STORE ADMIN</span>
            </div>
        </div>

        <nav aria-label="Main" class="flex flex-row flex-wrap lg:flex-col gap-1">
            @foreach ($items as $item)
                @if ($item['route'])
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                       class="flex items-center gap-3 min-h-11 px-3 rounded-lg transition-colors {{ $active ? 'bg-teal-500/15 text-white font-semibold' : 'text-slate-300 font-medium hover:bg-white/5 hover:text-white' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                        {{ $item['label'] }}
                    </a>
                @else
                    <span aria-disabled="true" class="flex items-center gap-3 min-h-11 px-3 rounded-lg text-slate-500 font-medium cursor-not-allowed">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                        {{ $item['label'] }}
                        <span class="ml-auto text-[10px] font-semibold tracking-wider rounded-full bg-white/5 px-2 py-0.5">SOON</span>
                    </span>
                @endif
            @endforeach
        </nav>

        <div class="lg:mt-auto flex flex-col gap-3 rounded-xl bg-white/5 p-3">
            <div class="flex flex-col gap-0.5 min-w-0">
                <span class="text-white font-semibold truncate">{{ $user->name }}</span>
                <span class="font-mono text-xs text-slate-400 truncate">{{ $user->email }}</span>
            </div>
            <form method="POST" action="{{ route('tenant.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 min-h-11 px-3 rounded-lg border border-[#2A3A52] text-slate-300 hover:text-white hover:border-slate-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4"/><path d="M10 8l-4 4 4 4"/><path d="M6 12h10"/></svg>
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0 px-4 py-8 sm:px-8 lg:px-10 flex flex-col gap-6">
        {{ $slot }}
    </main>
</div>
</body>
</html>
