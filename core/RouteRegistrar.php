<?php

declare(strict_types=1);

namespace Core;

/**
 * Route Registrar
 * 
 * Provides a fluent interface for adding middleware to routes.
 * 
 * Example:
 * Route::get('/dashboard', [Controller::class, 'index])
 *  ->middleware(AuthMiddleWare::class)
 *  ->middeware(AdminMiddleware::class);
 */
class RouteRegistrar
{
    protected $routeIndex;

    public function __construct($routeIndex)
    {
        $this->routeIndex = $routeIndex;
    }

    /**
     * Add middleware to this route
     * 
     * @param string|array $middleware Middleware class name(s)
     * @return $this
     */
    public function middleware($middleware)
    {
        Route::addMiddlewareToRoute($this->routeIndex, $middleware);
        return $this;
    }
}
