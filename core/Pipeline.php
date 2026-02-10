<?php

declare(strict_types=1);

namespace Core;

use Core\Http\Request;
use Core\Http\Response;
use Closure;

/**
 * Middleware Pipeline
 * 
 * Processes a request through a stack of middleware layers.
 * Each middleware can modify the request, call the next middleware,
 * and modify the response on the way back out.
 * 
 * Usage:
 * $pipeline = new Pipeline($container);
 * $response = $pipeline
 *   ->send($request)
 *   ->through([AuthMiddleware::class, CsrfMiddleware::class])
 *   ->then(function($request)) {
 *     // Final destination (controller)
 *     return $controller->method($request);
 *   }
 */
class Pipeline
{
    protected $container;
    protected $passable;
    protected $pipes = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set the object being passed through the pipeline
     * 
     * @param Request $passable
     * @return $this
     */
    public function send($passable)
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * Set the array of pipes (middleware classes)
     * 
     * @param array $pipes
     * @return $this
     */
    public function through(array $pipes)
    {
        $this->pipes = $pipes;
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback
     * 
     * @param Closure $destination
     * @return Response
     */
    public function then(Closure $destination)
    {

        // Build the pipeline from the inside out
        // Start with the final destination (controller)
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        // Execute the pipeline
        return $pipeline($this->passable);
    }

    /**
     * Get a CLosure that represents a slice of the pipeline
     * 
     * This is the core of the pipeline - it wraps each middleware
     * in a closure that calls the next middeware.
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                // Resolve the middleware from the container
                if (is_string($pipe)) {
                    $pipe = $this->container->resolve($pipe);
                }

                // Call the middleware's habdle method
                // Pass it the request and a closure to call the next layer
                return $pipe->handle($passable, $stack);
            };
        };
    }

    /**
     * Prepare the infal destination (controller action)
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }
}
