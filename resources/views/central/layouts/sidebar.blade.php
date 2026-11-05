@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'central.dashboard', 'match' => 'central.dashboard',
         'icon' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>'],
        ['label' => 'Tenants', 'route' => 'central.tenants.index', 'match' => 'central.tenants.*',
         'icon' => '<path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/>'],
        ['label' => 'Plans', 'route' => null, 'match' => null,
         'icon' => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>'],
        ['label' => 'Profile', 'route' => 'central.profile.edit', 'match' => 'central.profile.*',
         'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>'],
    ];
@endphp

<aside class="bg-ink text-slate-300 lg:w-60 lg:min-h-screen shrink-0 px-4 py-6 flex flex-col gap-7">
    <div class="flex items-center gap-2.5 px-2">
        <span class="w-[34px] h-[34px] rounded-[9px] bg-teal-500 text-ink flex items-center justify-center font-extrabold text-base">S</span>
        <div class="flex flex-col">
            <span class="text-white font-bold text-[15px]">{{ config('app.name') }}</span>
            <span class="font-mono text-[11px] tracking-wider text-slate-400">CENTRAL ADMIN</span>
        </div>
    </div>

    <nav aria-label="Main" class="flex flex-row flex-wrap lg:flex-col gap-1">
        @foreach ($items as $item)
            @php $active = $item['match'] && request()->routeIs($item['match']); @endphp
            @if ($item['route'])
                <a href="{{ route($item['route']) }}"
                   @if ($active) aria-current="page" @endif
                   class="flex items-center gap-3 min-h-11 px-3 rounded-lg no-underline transition-colors
                          {{ $active ? 'bg-teal-500/15 text-white font-semibold' : 'text-slate-300 font-medium hover:bg-white/5 hover:text-white' }}">
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
            <span class="text-white font-semibold truncate">{{ Auth::user()->name }}</span>
            <span class="font-mono text-xs text-slate-400 truncate">{{ Auth::user()->email }}</span>
        </div>
        <form method="POST" action="{{ route('central.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 min-h-11 px-3 rounded-lg border border-[#2A3A52] text-slate-300 hover:text-white hover:border-slate-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4"/><path d="M10 8l-4 4 4 4"/><path d="M6 12h10"/></svg>
                Log out
            </button>
        </form>
    </div>
</aside>
