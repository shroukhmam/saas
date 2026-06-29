<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tenancy middleware aliases — used in routes/tenant.php and routes/api.php
        $middleware->alias([
            'tenancy.domain'    => Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            'tenancy.subdomain' => Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
            'tenancy.header'    => Stancl\Tenancy\Middleware\InitializeTenancyByRequestData::class,
            'tenancy.block'     => Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,

            // Central admin guard — requires auth + is_admin flag
            'central.admin'     => App\Http\Middleware\Central\CentralAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
