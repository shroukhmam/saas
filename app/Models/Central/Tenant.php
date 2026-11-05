<?php

declare(strict_types=1);

namespace App\Models\Central;

use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    /**
     * Custom columns stored directly on the tenants table.
     * Everything else goes into the JSON 'data' column automatically
     * via stancl/virtualcolumn. A new real column must be listed here.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'status',
            'plan_id',
            'owner_email',
            'suspended_at',
            'created_at',
            'updated_at',
        ];
    }

    protected $casts = [
        'status' => TenantStatus::class,
        'suspended_at' => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(TenantSubscription::class)->current()->latestOfMany('starts_at');
    }

    public function isSuspended(): bool
    {
        return $this->status === TenantStatus::Suspended;
    }
}
