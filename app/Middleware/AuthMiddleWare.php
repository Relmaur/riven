<?php

namespace App\Middleware;

use Core\Interfaces\MiddlewareInterface;
use Core\Http\Request;
use Core\Http\RedirectResponse;
use Core\Session;
use Closure;

/**
 * Auth Middleware
 * 
 * Ensures the user isa uthenticated before allowing access to the route.
 * If not authenticated, redirects to the login page.
 * 
 * Usage in routes:
 * Route::get('/dashboard', [DashboardController::class, 'index])
 *   ->middleware(AuthMiddleware::class);
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {

        if (!Session::isAuthenticated()) {

            // Check if user is authenticated
            // Store the intended URL so we can redirect back
            Session::set('intended_url', $request->url());

            // Redirect to login
            Session::flash('error', 'You must be logged in to access this page.');
            return new RedirectResponse('/login');
        }
        
        // User is authenticated, continue to the next middleware/controller
        return $next($request);
    }
}
