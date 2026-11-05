<?php

use App\Http\Controllers\Central\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Central\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Central\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Central\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Central\Auth\NewPasswordController;
use App\Http\Controllers\Central\Auth\PasswordController;
use App\Http\Controllers\Central\Auth\PasswordResetLinkController;
use App\Http\Controllers\Central\Auth\RegisteredUserController;
use App\Http\Controllers\Central\Auth\VerifyEmailController;
use App\Http\Controllers\Central\DashboardController;
use App\Http\Controllers\Central\ProfileController;
use App\Http\Controllers\Central\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central (Landlord) Web Routes
|--------------------------------------------------------------------------
|
| All central admin routes live under the /admin prefix.
| These routes run on the central domain and use the public schema.
| Tenant routes are in routes/tenant.php — loaded by TenancyServiceProvider.
|
| The routes are bound to one domain (config('app.central_domain')).
| Without a domain, a tenant route with the same URI (for example "/")
| replaces the central route, and PreventAccessFromCentralDomains then
| returns 404 on the central domain.
|
*/

$centralDomain = config('app.central_domain');

// Other central domains (for example 127.0.0.1) redirect to the primary one.
// These routes have no names, so every route name stays unique.
$otherCentralDomains = array_diff(array_unique(config('tenancy.central_domains')), [$centralDomain]);

foreach ($otherCentralDomains as $domain) {
    Route::domain($domain)
        ->get('{path?}', function (Request $request) use ($centralDomain) {
            $port = in_array($request->getPort(), [80, 443], true) ? '' : ':'.$request->getPort();

            return redirect()->away($request->getScheme().'://'.$centralDomain.$port.$request->getRequestUri());
        })
        ->where('path', '.*');
}

Route::domain($centralDomain)->group(function () {

    // Redirect root to admin login
    Route::get('/', fn () => redirect()->route('central.login'));

    Route::prefix('admin')->name('central.')->group(function () {

        // ── Guest-only routes (login, register, password reset) ──────────────────
        Route::middleware('guest')->group(function () {

            Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');
            Route::post('login', [AuthenticatedSessionController::class, 'store']);

            Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');
            Route::post('register', [RegisteredUserController::class, 'store']);

            Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');
            Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

            Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');
            Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
        });

        // ── Authenticated routes (requires login + is_admin) ─────────────────────
        Route::middleware(['auth', 'central.admin'])->group(function () {

            // Email verification
            Route::get('verify-email', EmailVerificationPromptController::class)
                ->name('verification.notice');
            Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');
            Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

            // Password confirmation & update
            Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');
            Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
            Route::put('password', [PasswordController::class, 'update'])
                ->name('password.update');

            // Logout
            Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');

            // Dashboard
            Route::get('dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            // Tenants
            Route::resource('tenants', TenantController::class)->only(['index', 'create', 'store', 'destroy']);
            Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
            Route::post('tenants/{tenant}/restore', [TenantController::class, 'restore'])->name('tenants.restore');

            // Profile
            Route::get('profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');
            Route::patch('profile', [ProfileController::class, 'update'])
                ->name('profile.update');
            Route::delete('profile', [ProfileController::class, 'destroy'])
                ->name('profile.destroy');
        });
    });
});
