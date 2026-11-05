@props(['status'])

@php
    use App\Enums\TenantStatus;

    $classes = match ($status) {
        TenantStatus::Active => 'bg-green-100 text-green-800',
        TenantStatus::Trial => 'bg-blue-100 text-blue-800',
        TenantStatus::Suspended => 'bg-red-100 text-red-700',
        TenantStatus::Provisioning => 'bg-amber-100 text-amber-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold $classes"]) }}>
    <span class="w-1.5 h-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $status?->label() ?? 'Unknown' }}
</span>
