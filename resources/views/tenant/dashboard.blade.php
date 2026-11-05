<x-tenant-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <header class="flex flex-col gap-1.5">
        <span class="font-mono text-xs tracking-wider text-teal-700">STORE · {{ $tenant->database()->getName() }}</span>
        <h1 class="text-3xl font-extrabold tracking-tight">Welcome, {{ Auth::guard('tenant')->user()->name }}</h1>
        <p class="text-muted">This is the admin panel for {{ $tenant->name }}. Products, orders and settings come next.</p>
    </header>

    @if ($subscription?->trial_ends_at)
        @php $days = (int) ceil(now()->diffInDays($subscription->trial_ends_at, false)); @endphp
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-blue-900">
            Your free trial ends in <strong>{{ max($days, 0) }} {{ Str::plural('day', max($days, 0)) }}</strong>
            ({{ $subscription->trial_ends_at->format('j M Y') }}).
        </div>
    @endif

    <section aria-label="Store" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white border border-line rounded-xl p-5 flex flex-col gap-1.5">
            <span class="text-muted font-medium">Plan</span>
            <span class="text-2xl font-extrabold">{{ $tenant->plan?->name ?? '—' }}</span>
            <span class="text-xs text-muted">{{ $subscription?->status?->label() ?? 'No subscription' }}</span>
        </div>
        <div class="bg-white border border-line rounded-xl p-5 flex flex-col gap-1.5">
            <span class="text-muted font-medium">Store address</span>
            <span class="font-mono text-[13px] break-all">{{ request()->getHost() }}</span>
            <span class="text-xs text-muted">Status: {{ $tenant->status->label() }}</span>
        </div>
        <div class="bg-white border border-line rounded-xl p-5 flex flex-col gap-1.5">
            <span class="text-muted font-medium">Plan limits</span>
            <span class="font-semibold">{{ $tenant->plan?->max_products ?? 'Unlimited' }} products · {{ $tenant->plan?->max_staff ?? 'Unlimited' }} staff</span>
            <span class="text-xs text-muted">{{ $tenant->plan?->storage_gb ?? 'Unlimited' }} GB media</span>
        </div>
    </section>
</x-tenant-app-layout>
