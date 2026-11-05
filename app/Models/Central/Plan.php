<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Plan extends Model
{
    use CentralConnection;

    protected $fillable = [
        'name', 'slug', 'price', 'currency', 'billing_interval',
        'max_products', 'max_staff', 'storage_gb', 'custom_domain',
        'trial_days', 'is_default', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'max_products' => 'integer',
            'max_staff' => 'integer',
            'storage_gb' => 'integer',
            'custom_domain' => 'boolean',
            'trial_days' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /** NULL limit = unlimited. */
    public function allows(string $limit, int $current): bool
    {
        return $this->{$limit} === null || $current < $this->{$limit};
    }
}
