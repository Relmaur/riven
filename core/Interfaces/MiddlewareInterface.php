<?php

declare(strict_types=1);

namespace Core\Interfaces;

use Core\Http\Request;
use Core\Http\Response;
use Closure;

/**
 * Middleware Interface
 * 
 * All middleware must implement this interface.
 * The handle method receives the request and a $next callable.
 * 
 * Example:
 * public function handle(Request $request, Closure $next)
 * {
 *     // Do something before the controller
 *     if (!authenticated()) {
 *         return redirect('/login');
 *     }
 *     
 *     // Pass to next middleware/controller
 *     $response = $next($request);
 *     
 *     // Do something after the controller
 *     $response->headers['X-Custom'] = 'value';
 *     
 *     return $response;
 * }
 */
interface MiddlewareInterface
{
    /**
     * Handle an incoming request
     * 
     * @param Request $request The incoming request
     * @param Closure $next The next middleware in the pipeline
     * @return Response
     */
    public function handle(Request $request, Closure $next);
}
