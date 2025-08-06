<?php

namespace App\Controllers;

use Core\View;

class PagesController
{

    public function home()
    {
        $pageTitle = "Welcome to the Homepage!";
        $pageContent = "This is the content of the homepage, loaded from the PagesController.";

        View::render('pages/home', [
            'pageTitle' => $pageTitle,
            'pageContent' => $pageContent
        ]);
    }

    public function about()
    {
        $pageTitle = "About Us";
        $pageContent = "This page contains information about our application.";

        View::render('pages/about', [
            'pageTitle' => $pageTitle,
            'pageContent' => $pageContent
        ]);
    }
}
