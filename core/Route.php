<?php

namespace Core;

class Route
{
    protected static $routes = [];

    public static function get($uri, $action)
    {
        self::add('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        self::add('POST', $uri, $action);
    }

    protected static function add($method, $uri, $action)
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public static function getRoutes()
    {
        return self::$routes;
    }
}
