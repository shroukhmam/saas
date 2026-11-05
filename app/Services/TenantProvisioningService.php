<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Enums\TenantStatus;
use App\Models\Central\Plan;
use App\Models\Central\Tenant;
use App\Models\Central\TenantSubscription;
use App\Models\Tenant\User as TenantUser;
use Illuminate\Support\Str;
use Throwable;

/**
 * Creates a store end to end:
 *   1. tenant row (status = provisioning)
 *      → TenantCreated pipeline: CREATE SCHEMA tenant_<id> + tenant migrations
 *   2. domain
 *   3. owner account inside the tenant schema
 *   4. subscription (trial or active)
 *   5. status = trial | active
 *
 * If any step fails, the tenant is deleted. TenantDeleted drops the schema,
 * so no half-made store stays behind.
 */
class TenantProvisioningService
{
    /**
     * @param  array{id: string, name: string, domain: string, plan_id: int, owner_name: string, owner_email: string, trial: bool}  $data
     * @return array{tenant: Tenant, owner_password: string}
     */
    public function provision(array $data): array
    {
        $plan = Plan::findOrFail($data['plan_id']);
        $ownerPassword = Str::password(16, symbols: false);

        try {
            $tenant = Tenant::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'status' => TenantStatus::Provisioning,
                'plan_id' => $plan->id,
                'owner_email' => $data['owner_email'],
            ]);

            $tenant->domains()->create(['domain' => $data['domain']]);

            $tenant->run(function () use ($data, $ownerPassword) {
                TenantUser::create([
                    'name' => $data['owner_name'],
                    'email' => $data['owner_email'],
                    'password' => $ownerPassword,
                    'is_owner' => true,
                ]);
            });

            $onTrial = $data['trial'] && $plan->trial_days > 0;

            TenantSubscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'status' => $onTrial ? SubscriptionStatus::Trialing : SubscriptionStatus::Active,
                'starts_at' => now(),
                'trial_ends_at' => $onTrial ? now()->addDays($plan->trial_days) : null,
            ]);

            $tenant->update(['status' => $onTrial ? TenantStatus::Trial : TenantStatus::Active]);
        } catch (Throwable $e) {
            // Tenant::create() can fail after the row and schema exist
            // (for example in a tenant migration), so look it up again.
            Tenant::find($data['id'])?->delete();

            throw $e;
        }

        return ['tenant' => $tenant, 'owner_password' => $ownerPassword];
    }

    public function suspend(Tenant $tenant): void
    {
        $tenant->update(['status' => TenantStatus::Suspended, 'suspended_at' => now()]);
    }

    public function restore(Tenant $tenant): void
    {
        $onTrial = $tenant->currentSubscription?->status === SubscriptionStatus::Trialing;

        $tenant->update([
            'status' => $onTrial ? TenantStatus::Trial : TenantStatus::Active,
            'suspended_at' => null,
        ]);
    }

    /** Deletes the tenant. TenantDeleted drops its schema; subscriptions cascade. */
    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
