<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Interfaces\MiddlewareInterface;
use Core\Http\Request;
use Core\Http\RedirectResponse;
use Core\Session;
use Closure;

/**
 * Guest Middleware
 * 
 * Ensures the user is a guest (not authenticated) before allowing access to the route.
 * If authenticated, redirects to the dashboard or another appropriate page.
 * 
 * Usage in routes:
 * Route::get('/dashboard', [DashboardController::class, 'index])
 *   ->middleware(GuestMiddleware::class);
 */
class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {

        // If user is authenticated, they shouldn't access guest-only pages
        if (Session::isAuthenticated()) {
            return new RedirectResponse('/dashboard'); // Redirect authenticated users to dashboard
        }

        // User is a guest, continue
        return $next($request);
    }
}