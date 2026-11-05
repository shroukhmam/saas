<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the central (landlord) database.
     * Every seeder here is safe to run more than once.
     * Tenant data is seeded per tenant during provisioning, not here.
     */
    public function run(): void
    {
        $this->call([
            CentralAdminSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
