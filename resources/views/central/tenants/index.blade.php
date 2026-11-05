@php
    use App\Enums\TenantStatus;

    $tabs = array_merge([['value' => null, 'label' => 'All', 'count' => $total]],
        array_map(fn (TenantStatus $s) => ['value' => $s->value, 'label' => $s->label(), 'count' => $counts[$s->value] ?? 0], TenantStatus::cases()));
    $port = in_array(request()->getPort(), [80, 443], true) ? '' : ':'.request()->getPort();
@endphp

<x-central-app-layout>
    <x-slot name="title">Tenants</x-slot>

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="flex flex-col gap-1.5">
            <span class="font-mono text-xs tracking-wider text-teal-700">TENANTS · one schema each</span>
            <h1 class="text-3xl font-extrabold tracking-tight">Tenants</h1>
            <p class="text-muted">Create, suspend, or delete a store. Deleting a store drops its schema.</p>
        </div>
        <a href="{{ route('central.tenants.create') }}"
           class="inline-flex items-center gap-2 min-h-11 px-[18px] rounded-[10px] bg-teal-700 text-white font-semibold hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            New tenant
        </a>
    </div>

    {{-- Owner credentials: shown once, right after provisioning --}}
    @if ($creds = session('owner_credentials'))
        <section x-data="{ copied: false }" class="rounded-xl border border-amber-300 bg-amber-50 p-4 flex flex-col gap-2">
            <h2 class="font-bold text-amber-900">Owner sign-in details — shown only once</h2>
            <p class="text-amber-900">Send these to the store owner in a safe way. The password is not stored in plain text, so this page cannot show it again.</p>
            <dl class="grid gap-1 sm:grid-cols-[auto_1fr] sm:gap-x-4 font-mono text-[13px]">
                <dt class="text-amber-800">Store</dt><dd>http://{{ $creds['domain'] }}{{ $port }}</dd>
                <dt class="text-amber-800">Email</dt><dd>{{ $creds['email'] }}</dd>
                <dt class="text-amber-800">Password</dt><dd><span x-ref="pw">{{ $creds['password'] }}</span>
                    <button type="button" class="ml-2 text-xs font-semibold text-amber-900 underline"
                            @click="navigator.clipboard.writeText($refs.pw.textContent).then(() => copied = true)"
                            x-text="copied ? 'Copied' : 'Copy'">Copy</button></dd>
            </dl>
        </section>
    @endif

    <section class="bg-white border border-line rounded-xl flex flex-col">
        <form method="GET" action="{{ route('central.tenants.index') }}" class="flex flex-wrap items-center justify-between gap-3 px-4 py-3.5 border-b border-line">
            <div role="tablist" aria-label="Filter by status" class="flex flex-wrap gap-1 bg-[#EEF1F4] rounded-[10px] p-1">
                @foreach ($tabs as $tab)
                    @php $on = ($status?->value) === $tab['value']; @endphp
                    <a role="tab" aria-selected="{{ $on ? 'true' : 'false' }}"
                       href="{{ route('central.tenants.index', array_filter(['status' => $tab['value'], 'q' => $search ?: null, 'plan' => $planId])) }}"
                       class="inline-flex items-center gap-1.5 min-h-9 px-3.5 rounded-lg font-semibold {{ $on ? 'bg-white text-ink shadow-sm' : 'text-muted hover:text-ink' }}">
                        {{ $tab['label'] }} <span class="tabular-nums opacity-70">{{ $tab['count'] }}</span>
                    </a>
                @endforeach
            </div>
            <div class="flex flex-wrap gap-2.5">
                @if ($status)<input type="hidden" name="status" value="{{ $status->value }}">@endif
                <label for="tenant-search" class="flex items-center gap-2 min-h-11 px-3 border border-line rounded-[10px] text-muted focus-within:ring-2 focus-within:ring-teal-500">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                    <span class="sr-only">Search tenants</span>
                    <input id="tenant-search" name="q" type="search" value="{{ $search }}" placeholder="Store, domain or ID"
                           class="border-0 p-0 focus:ring-0 text-sm text-ink w-44 bg-transparent">
                </label>
                <label for="plan-filter" class="sr-only">Plan</label>
                <select id="plan-filter" name="plan" onchange="this.form.submit()"
                        class="min-h-11 rounded-[10px] border-line text-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">All plans</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected($planId === $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        @if ($tenants->isEmpty())
            <div class="flex flex-col items-center gap-3 px-6 py-16 text-center">
                <span class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/></svg>
                </span>
                @if ($total === 0)
                    <h2 class="text-base font-bold">No stores yet</h2>
                    <p class="text-muted max-w-sm">Create the first store. The platform makes its schema, runs the migrations and creates the owner account.</p>
                    <a href="{{ route('central.tenants.create') }}" class="font-semibold text-teal-700 hover:text-teal-800">Create the first tenant →</a>
                @else
                    <h2 class="text-base font-bold">No stores match these filters</h2>
                    <a href="{{ route('central.tenants.index') }}" class="font-semibold text-teal-700 hover:text-teal-800">Clear filters</a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] border-collapse">
                    <thead>
                        <tr class="text-left text-muted text-xs tracking-wider">
                            <th class="px-4 py-3 font-semibold">STORE</th>
                            <th class="px-2 py-3 font-semibold">DOMAIN</th>
                            <th class="px-2 py-3 font-semibold">SCHEMA</th>
                            <th class="px-2 py-3 font-semibold">PLAN</th>
                            <th class="px-2 py-3 font-semibold">STATUS</th>
                            <th class="px-2 py-3 font-semibold">CREATED</th>
                            <th class="px-4 py-3 font-semibold text-right">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($tenants as $tenant)
                        @php $domain = $tenant->domains->first()?->domain; @endphp
                        <tr class="border-t border-[#EEF1F4] align-middle" x-data="{ confirming: false }">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center font-bold">{{ mb_strtoupper(mb_substr($tenant->name, 0, 1)) }}</span>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-semibold">{{ $tenant->name }}</span>
                                        <span class="text-xs text-muted">{{ $tenant->owner_email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-2 py-3.5 font-mono text-[13px] text-slate-700">{{ $domain ?? '—' }}</td>
                            <td class="px-2 py-3.5 font-mono text-[13px] text-muted">{{ $tenant->database()->getName() }}</td>
                            <td class="px-2 py-3.5">{{ $tenant->plan?->name ?? '—' }}</td>
                            <td class="px-2 py-3.5"><x-central.status-badge :status="$tenant->status" /></td>
                            <td class="px-2 py-3.5 text-muted tabular-nums">{{ $tenant->created_at?->format('j M Y') }}</td>
                            <td class="px-4 py-2.5">
                                <div class="flex justify-end items-center gap-1.5" x-show="!confirming">
                                    @if ($domain)
                                        <a href="http://{{ $domain }}{{ $port }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center min-h-9 px-3 border border-line rounded-lg font-semibold text-[13px] text-teal-700 hover:bg-ground">Open</a>
                                    @endif
                                    @if ($tenant->isSuspended())
                                        <form method="POST" action="{{ route('central.tenants.restore', $tenant) }}">@csrf
                                            <button class="min-h-9 px-3 border border-line rounded-lg font-semibold text-[13px] text-teal-700 hover:bg-ground">Restore</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('central.tenants.suspend', $tenant) }}">@csrf
                                            <button class="min-h-9 px-3 border border-line rounded-lg font-semibold text-[13px] text-red-700 hover:bg-red-50">Suspend</button>
                                        </form>
                                    @endif
                                    <button type="button" @click="confirming = true" aria-label="Delete {{ $tenant->name }}"
                                            class="min-h-9 px-2.5 border border-line rounded-lg text-muted hover:text-red-700 hover:bg-red-50">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>
                                    </button>
                                </div>
                                <form x-show="confirming" x-cloak method="POST" action="{{ route('central.tenants.destroy', $tenant) }}"
                                      class="flex justify-end items-center gap-1.5">
                                    @csrf @method('DELETE')
                                    <label for="confirm-{{ $tenant->id }}" class="sr-only">Type {{ $tenant->id }} to confirm</label>
                                    <input id="confirm-{{ $tenant->id }}" name="confirm_id" placeholder="Type {{ $tenant->id }}" autocomplete="off"
                                           class="min-h-9 w-40 rounded-lg border-red-300 text-[13px] font-mono focus:border-red-500 focus:ring-red-500">
                                    <button class="min-h-9 px-3 rounded-lg bg-red-700 text-white font-semibold text-[13px] hover:bg-red-800">Delete</button>
                                    <button type="button" @click="confirming = false" class="min-h-9 px-2 text-muted font-semibold text-[13px]">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3.5 border-t border-line text-muted">
                <span>Showing {{ $tenants->firstItem() }}–{{ $tenants->lastItem() }} of {{ $tenants->total() }} tenants</span>
                {{ $tenants->links() }}
            </div>
        @endif
    </section>
</x-central-app-layout>
