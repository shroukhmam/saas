<?php

declare(strict_types=1);

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    /**
     * Custom columns stored directly on the tenants table.
     * Everything else goes into the JSON 'data' column automatically
     * via stancl/virtualcolumn.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
        ];
    }
}
