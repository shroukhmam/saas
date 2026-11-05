<?php

namespace Database\Seeders;

use App\Models\Central\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * TEMPORARY NUMBERS — prices and limits are placeholders until the
     * real pricing is decided. Change them here and run the seeder again;
     * it updates plans by slug and does not create duplicates.
     *
     * A NULL limit means "unlimited".
     */
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price' => 199,          // TEMPORARY
                'max_products' => 100,   // TEMPORARY
                'max_staff' => 1,
                'storage_gb' => 1,       // TEMPORARY
                'custom_domain' => false,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'growth',
                'name' => 'Growth',
                'price' => 499,          // TEMPORARY
                'max_products' => 1000,  // TEMPORARY
                'max_staff' => 5,
                'storage_gb' => 10,      // TEMPORARY
                'custom_domain' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'slug' => 'scale',
                'name' => 'Scale',
                'price' => 999,          // TEMPORARY
                'max_products' => null,  // unlimited
                'max_staff' => 20,
                'storage_gb' => 50,      // TEMPORARY
                'custom_domain' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan + ['currency' => 'EGP', 'billing_interval' => 'month', 'trial_days' => 14, 'is_active' => true],
            );
        }

        $this->command?->info('Plans seeded (temporary prices): Starter, Growth, Scale');
    }
}
