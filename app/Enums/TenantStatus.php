<?php

namespace App\Enums;

enum TenantStatus: string
{
    case Provisioning = 'provisioning'; // schema and migrations are running
    case Trial = 'trial';
    case Active = 'active';
    case Suspended = 'suspended';       // storefront, panel and API are blocked

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** Only these statuses may open the tenant panel and API. */
    public function canAccess(): bool
    {
        return in_array($this, [self::Trial, self::Active], true);
    }
}
