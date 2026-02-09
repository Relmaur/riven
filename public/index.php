<?php

require_once '../vendor/autoload.php';

use Core\Router;
use Core\Session;
use Core\Http\Request;

// Get the container with all the bindings
$container = require_once '../bootstrap.php';

// Start the session on every request
Session::start();

// Capture the current request
$request = Request::capture();

require_once '../routes/web.php';
require_once '../routes/api.php';

// Dispatch the router
$router = new Router($container, $request);

$response = $router->dispatch();

$response->send();
