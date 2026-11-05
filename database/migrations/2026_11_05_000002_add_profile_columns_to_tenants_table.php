<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Real columns for the data the central admin lists and filters on.
     * Each one must also be in Tenant::getCustomColumns(), or stancl/tenancy
     * stores it in the JSON "data" column instead.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('status')->default('provisioning')->index()->after('name'); // App\Enums\TenantStatus
            $table->foreignId('plan_id')->nullable()->after('status')
                ->constrained('plans')->restrictOnDelete();
            // The owner account lives in the tenant schema. A copy of the email
            // here lets the central admin list owners without entering each schema.
            $table->string('owner_email')->nullable()->after('plan_id');
            $table->timestamp('suspended_at')->nullable()->after('owner_email');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['name', 'status', 'owner_email', 'suspended_at']);
        });
    }
};
