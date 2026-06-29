<?php

namespace App\Http\Middleware\Central;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralAdminMiddleware
{
    /**
     * Only allow authenticated central admin users through.
     * Blocks unauthenticated requests and non-admin users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('central.login');
        }

        if (! $request->user()->is_admin) {
            abort(403, 'Access restricted to central administrators.');
        }

        return $next($request);
    }
}
