<?php

declare(strict_types=1);

namespace Core;

use Core\Route;
use Core\View;
use Core\Http\Request;
use Core\Http\Response;
use Closure;

class Router
{
    protected $container;
    protected $request;

    public function __construct(Container $container, Request $request = null)
    {
        $this->container = $container;
        // Use provided request or capture from superglobals
        $this->request = $request ?? Request::capture();
    }

    public function dispatch()
    {
        $uri = $this->request->path();
        $method = $this->request->method();

        foreach (Route::getRoutes() as $route) {

            // Convert URI to a regex pattern
            $pattern = $this->convertRouteToRegex($route['uri']);

            // Checks if the current request URI matches the pattern and the method is correct
            if ($route['method'] == $method && preg_match($pattern, $uri, $matches)) {

                // Remove the full match from the beginning of the array
                array_shift($matches);

                // Extract action and call it
                $action = $route['action'];
                if (is_array($action) && count($action) === 2) {
                    $controllerName = $action[0];
                    $methodName = $action[1];

                    if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {

                        // Run through middleware pipeline
                        return $this->runThroughMiddleware(
                            $route['middleware'] ?? [],
                            function ($request) use ($controllerName, $methodName, $matches) {
                                $controllerInstance = $this->container->resolve($controllerName);

                                // Use reflection to inject Request where type-hinted
                                // and route parameters everywhere else
                                $args = $this->resolveMethodArgs($controllerInstance, $methodName, $request, $matches);

                                return call_user_func_array(
                                    [$controllerInstance, $methodName],
                                    $args
                                );
                            }
                        );
                    }
                }
                // Controller/method not found - 404
                return View::render('errors/404', ['pageTitle' => 'Not Found']);
            }
        }

        // No route matched - 404
        return View::render('errors/404', ['pageTitle' => 'Not Found']);
    }

    /**
     * Run the request through the middleware pipeline
     * 
     * @param array $middleware Array of middleware class names
     * @param Closure $destination The final controller action
     * @return Response
     */
    protected function runThroughMiddleware(array $middleware, Closure $destination)
    {
        $pipeline = new Pipeline($this->container);

        return $pipeline
            ->send($this->request)
            ->through($middleware)
            ->then($destination);
    }

    /**
     * Resolve method arguments by injecting Request where type-hinted
     * and filling remaining parameters with route matches.
     */
    private function resolveMethodArgs($controller, string $method, Request $request, array $routeParams): array
    {
        $reflection = new \ReflectionMethod($controller, $method);
        $args = [];
        $routeParams = array_values($routeParams);

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && is_a($request, $type->getName())) {
                $args[] = $request;
            } else {
                $args[] = array_shift($routeParams);
            }
        }

        return $args;
    }

    private function convertRouteToRegex($uri)
    {
        // Convert route placeholders like {id} to a regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $uri);
        // Escape forward slashes and add start/end anchors
        return '#^' . str_replace('/', '\/', $pattern) . '\/?$#';
    }
}
