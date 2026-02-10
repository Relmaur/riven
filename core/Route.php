<?php

declare(strict_types=1);

namespace Core;

use Closure;

class Route
{
    protected static $routes = [];
    protected static $groupStack = [];

    public static function get($uri, $action)
    {
        return self::add('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        return self::add('POST', $uri, $action);
    }

    /**
     * Create a route group with shared attributes
     * 
     * Example:
     * Route::group(['middleware' => ['auth], 'prefix' => '/admin'], function() {
     *  Route::get('/dashboard', [AdminController::class, 'index']);
     * })
     */
    public static function group(array $attributes, Closure $callback)
    {

        // Push attributes onto the group stack
        self::$groupStack[] = $attributes;

        // Execute the callback to register routes
        $callback();

        // Pop the group off the stack
        array_pop(self::$groupStack);
    }

    /**
     * Add a route and return a RouteRegistrar for chainging
     */
    protected static function add($method, $uri, $action)
    {

        // Merge group attributes
        $attributes = self::mergeGroupAttributes();

        // Apply prefix to URI
        if (isset($attributes['prefix'])) {
            $uri = trim($attributes['prefix'], '/') . '/' . trim($uri, '/');
        }

        // Build route definition
        $route = [
            'method' => $method,
            'uri' => '/' . trim($uri, '/'),
            'action' => $action,
            'middleware' => $attributes['middleware'] ?? []
        ];

        self::$routes[] = $route;

        // Return a route registrar for method chaining (e.g. ->middleware())
        return new RouteRegistrar(count(self::$routes) - 1);
    }

    /**
     * Merge attributes from the group stack}
     */
    protected static function mergeGroupAttributes()
    {
        $attributes = [
            'prefix' => '',
            'middleware' => []
        ];

        foreach (self::$groupStack as $group) {
            // Merge prefixed
            if (isset($group['prefix'])) {
                $attributes['prefix'] = trim($attributes['prefix'], '/') . '/' . trim($group['prefix'], '/');
            }

            // Merge middleware arrays
            if (isset($group['middleware'])) {
                $middleware = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
                $attributes['middleware'] = array_merge($attributes['middleware'], $middleware);
            }
        }
        return $attributes;
    }

    /**
     * Add middleware to a specific route (called by RouteRegistrar)
     */
    public static function addMiddlewareToRoute($index, $middleware)
    {
        if (!is_array($middleware)) {
            $middleware = [$middleware];
        }

        self::$routes[$index]['middleware'] = array_merge(
            self::$routes[$index]['middleware'],
            $middleware
        );
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
