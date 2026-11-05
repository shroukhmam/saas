<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trialing = 'trialing';
    case Active = 'active';
    case PastDue = 'past_due'; // payment failed
    case Canceled = 'canceled';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
