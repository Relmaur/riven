<?php

namespace App\Controllers;

use Core\Session;


// The abstract keyword means that this class can't be directly instantiated, only extended.
abstract class BaseController
{
    public function __construct()
    {
        // This constructor gets called for any controller that extends BaseController
    }

    /**
     * Middleware-like check to ensure user is authenticated. If not, redirects to the login page.
     */
    protected function requireAuth()
    {
        if (!Session::isAuthenticated()) {
            header('Location: /users/login');
            exit();
        }
    }
}
