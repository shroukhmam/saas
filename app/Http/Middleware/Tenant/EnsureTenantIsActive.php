<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks a store that is suspended or still provisioning.
 * Put it AFTER the tenancy initialization middleware.
 */
class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant && ! $tenant->status?->canAccess()) {
            $message = $tenant->isSuspended()
                ? 'This store is suspended. Contact the platform administrator.'
                : 'This store is not ready yet. Try again in a few minutes.';

            abort($tenant->isSuspended() ? 403 : 503, $message);
        }

        return $next($request);
    }
}
