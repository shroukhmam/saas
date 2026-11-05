<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Subscription plans (central DB, public schema).
     * A NULL limit means "unlimited".
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('billing_interval')->default('month'); // month | year

            // Limits applied to every store on the plan
            $table->unsignedInteger('max_products')->nullable();
            $table->unsignedInteger('max_staff')->nullable();
            $table->unsignedInteger('storage_gb')->nullable();
            $table->boolean('custom_domain')->default(false);

            $table->unsignedSmallInteger('trial_days')->default(14);
            $table->boolean('is_default')->default(false); // plan picked first on "New tenant"
            $table->boolean('is_active')->default(true);   // inactive plans are hidden for new tenants
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
