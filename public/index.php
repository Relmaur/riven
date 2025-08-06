<?php

require_once '../vendor/autoload.php';

use Core\Router;
use Core\Session;

// Start the session on every requrest
Session::start();

require_once '../routes/web.php';

// Dispatch the router
$router = new Router();
$router->dispatch();
