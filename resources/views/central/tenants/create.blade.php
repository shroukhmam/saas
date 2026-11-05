@php
    $defaultPlan = old('plan_id', $plans->firstWhere('is_default', true)?->id ?? $plans->first()?->id);
    $limit = fn ($v, $unit = '') => $v === null ? 'Unlimited' : number_format($v).$unit;
@endphp

<x-central-app-layout>
    <x-slot name="title">New tenant</x-slot>

    <header class="flex flex-col gap-1.5">
        <a href="{{ route('central.tenants.index') }}" class="text-[13px] font-semibold text-teal-700 hover:text-teal-800">← Tenants</a>
        <h1 class="text-3xl font-extrabold tracking-tight">New tenant</h1>
        <p class="text-muted">Create a store. The platform makes its schema, runs the tenant migrations, and creates the owner account.</p>
    </header>

    <div class="flex flex-wrap gap-5 items-start"
         x-data="{
            name: @js(old('name', '')),
            id: @js(old('id', '')),
            sub: @js(old('subdomain', '')),
            idTouched: @js(old('id') !== null),
            subTouched: @js(old('subdomain') !== null),
            plan: @js((string) $defaultPlan),
            plans: @js($plans->pluck('name', 'id')),
            slug(v, sep) { return v.toLowerCase().normalize('NFKD').replace(/[^a-z0-9]+/g, sep).replace(new RegExp('^' + sep + '+|' + sep + '+$', 'g'), '').slice(0, 40); },
            onName() {
                if (!this.idTouched) { let s = this.slug(this.name, '_'); this.id = /^[a-z]/.test(s) ? s : (s ? 's_' + s : ''); }
                if (!this.subTouched) this.sub = this.id.replace(/_/g, '-');
            },
            get schema() { return 'tenant_' + (this.id || '…'); },
            get domain() { return (this.sub || '…') + '.{{ $centralDomain }}'; },
         }">

        <form method="POST" action="{{ route('central.tenants.store') }}" class="flex-[999_1_520px] min-w-0 flex flex-col gap-5" novalidate>
            @csrf

            <fieldset class="border border-line rounded-xl bg-white p-5 flex flex-col gap-4">
                <legend class="px-1.5 text-base font-bold">Store</legend>

                <div class="flex flex-col gap-1.5">
                    <label for="name" class="font-semibold">Store name</label>
                    <input id="name" name="name" type="text" x-model="name" @input="onName()" required maxlength="100" autofocus
                           class="min-h-11 rounded-[10px] border-slate-300 focus:border-teal-500 focus:ring-teal-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="id" class="font-semibold">Tenant ID</label>
                        <input id="id" name="id" type="text" x-model="id" @input="idTouched = true; if (!subTouched) sub = id.replace(/_/g, '-')" required
                               class="min-h-11 rounded-[10px] border-slate-300 font-mono text-[13px] focus:border-teal-500 focus:ring-teal-500 @error('id') border-red-500 @enderror">
                        @error('id')<p class="text-xs text-red-700">{{ $message }}</p>@else<p class="text-xs text-muted">Lowercase letters, numbers, underscores. You cannot change it later: the schema name uses it.</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="subdomain" class="font-semibold">Subdomain</label>
                        <div class="flex items-stretch rounded-[10px] border border-slate-300 focus-within:border-teal-500 focus-within:ring-1 focus-within:ring-teal-500 overflow-hidden @error('subdomain') border-red-500 @enderror @error('domain') border-red-500 @enderror">
                            <input id="subdomain" name="subdomain" type="text" x-model="sub" @input="subTouched = true" required
                                   class="flex-1 min-w-0 min-h-11 border-0 font-mono text-[13px] focus:ring-0">
                            <span class="flex items-center px-3 bg-ground text-muted font-mono text-[13px] border-l border-line">.{{ $centralDomain }}</span>
                        </div>
                        @error('subdomain')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                        @error('domain')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="flex flex-col gap-1 rounded-[10px] bg-ground px-3.5 py-3">
                        <span class="text-[11px] font-semibold tracking-wider text-muted">DOMAIN</span>
                        <span class="font-mono text-[13px] break-all" x-text="domain"></span>
                    </div>
                    <div class="flex flex-col gap-1 rounded-[10px] bg-ground px-3.5 py-3">
                        <span class="text-[11px] font-semibold tracking-wider text-muted">POSTGRESQL SCHEMA</span>
                        <span class="font-mono text-[13px] break-all" x-text="schema"></span>
                    </div>
                </div>
            </fieldset>

            <fieldset class="border border-line rounded-xl bg-white p-5 flex flex-col gap-4">
                <legend class="px-1.5 text-base font-bold">Owner account</legend>
                <p class="text-muted">This person becomes the store owner. The account lives in the tenant schema, not in the central users table. You get a one-time password after you create the store.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="owner_name" class="font-semibold">Full name</label>
                        <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required maxlength="100"
                               class="min-h-11 rounded-[10px] border-slate-300 focus:border-teal-500 focus:ring-teal-500 @error('owner_name') border-red-500 @enderror">
                        @error('owner_name')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="owner_email" class="font-semibold">Email</label>
                        <input id="owner_email" name="owner_email" type="email" value="{{ old('owner_email') }}" required
                               class="min-h-11 rounded-[10px] border-slate-300 focus:border-teal-500 focus:ring-teal-500 @error('owner_email') border-red-500 @enderror">
                        @error('owner_email')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="border border-line rounded-xl bg-white p-5 flex flex-col gap-4">
                <legend class="px-1.5 text-base font-bold">Plan</legend>
                @error('plan_id')<p class="text-xs text-red-700">{{ $message }}</p>@enderror
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ($plans as $plan)
                        <label class="flex flex-col gap-2 p-4 rounded-xl cursor-pointer border transition-colors"
                               :class="plan === '{{ $plan->id }}' ? 'border-2 border-teal-700 bg-teal-50/60' : 'border-line bg-white m-px'">
                            <span class="flex items-center justify-between gap-2">
                                <span class="font-bold text-[15px]">{{ $plan->name }}</span>
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" x-model="plan" class="w-[18px] h-[18px] text-teal-700 focus:ring-teal-500">
                            </span>
                            <span class="font-bold">{{ rtrim(rtrim(number_format($plan->price, 2), '0'), '.') }} {{ $plan->currency }} <span class="font-medium text-muted">/ {{ $plan->billing_interval }}</span></span>
                            <span class="text-xs text-muted">{{ $limit($plan->max_products) }} products · {{ $limit($plan->max_staff) }} staff · {{ $limit($plan->storage_gb, ' GB') }} media</span>
                        </label>
                    @endforeach
                </div>
                <label for="trial" class="flex items-center gap-2.5 min-h-11">
                    <input type="hidden" name="trial" value="0">
                    <input id="trial" name="trial" type="checkbox" value="1" @checked(old('trial', true)) class="w-[18px] h-[18px] rounded text-teal-700 focus:ring-teal-500">
                    Start with a free trial ({{ $plans->first()?->trial_days ?? 14 }} days)
                </label>
            </fieldset>

            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('central.tenants.index') }}" class="inline-flex items-center min-h-11 px-[18px] rounded-[10px] border border-slate-300 bg-white font-semibold hover:bg-ground">Cancel</a>
                <button type="submit" class="min-h-11 px-[22px] rounded-[10px] bg-teal-700 text-white font-bold hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2">Create tenant</button>
            </div>
        </form>

        <aside aria-label="What happens next" class="flex-[1_1_300px] min-w-0 bg-ink text-slate-300 rounded-xl p-5 flex flex-col gap-4">
            <h2 class="text-white text-base font-bold">When you create the tenant</h2>
            <ol class="flex flex-col gap-3.5">
                @foreach ([
                    ['Create the schema', "'CREATE SCHEMA ' + schema"],
                    ['Run tenant migrations', "'database/migrations-tenant → ' + schema"],
                    ['Create the owner', "schema + '.users · is_owner'"],
                    ['Start the subscription', "(plans[plan] || '') + ' plan'"],
                    ['Attach the domain', 'domain'],
                ] as $i => [$title, $expr])
                    <li class="flex gap-3">
                        <span class="flex-none w-[26px] h-[26px] rounded-full border border-[#2E4560] text-teal-300 flex items-center justify-center font-mono text-xs">{{ $i + 1 }}</span>
                        <span class="flex flex-col gap-0.5 min-w-0">
                            <span class="text-white font-semibold">{{ $title }}</span>
                            <span class="font-mono text-xs text-slate-400 break-all" x-text="{{ $expr }}"></span>
                        </span>
                    </li>
                @endforeach
            </ol>
            <p class="text-xs text-slate-400 border-t border-[#22344B] pt-3.5">If a step fails, the platform drops the new schema, so no half-made store stays behind.</p>
        </aside>
    </div>
</x-central-app-layout>
