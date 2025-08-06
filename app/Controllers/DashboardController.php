<?php

namespace App\Controllers;

use Core\View;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function home()
    {
        View::render('dashboard/home', [
            'pageTitle' => 'ML CMS | Dashboard'
        ], 'dashboard');
    }
}
