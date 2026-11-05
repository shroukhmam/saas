<?php

namespace App\Models\Central;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class TenantSubscription extends Model
{
    use CentralConnection;

    protected $fillable = [
        'tenant_id', 'plan_id', 'status',
        'trial_ends_at', 'starts_at', 'ends_at', 'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'trial_ends_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /** Subscriptions that are running now (not ended, not canceled). */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereNull('canceled_at')
            ->where('starts_at', '<=', now())
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));
    }

    /** Trials that end within the next $days days (Plans page list). */
    public function scopeTrialEndingWithin(Builder $query, int $days): Builder
    {
        return $query->where('status', SubscriptionStatus::Trialing)
            ->whereBetween('trial_ends_at', [now(), now()->addDays($days)]);
    }
}
