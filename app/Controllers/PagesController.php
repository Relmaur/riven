<?php 

namespace App\Controllers;

class PagesController {
    
    public function home() {
        $pageTitle = "Welcome to the Homepage!";
        $pageContent = "This is the content of the homepage, loaded from the PagesController.";

        require_once '../app/Views/pages/home.php';
    }

    public function about() {
        $pageTitle = "About Us";
        $pageContent = "This page contains information about our application.";

        require_once '../app/Views/pages/about.php';
    }
}