<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\View;

class WebpageController
{
    public function index()
    {
        if (!Auth::isAuthorized()) {
            return new Redirect('/login');
        }
        return new View('Webpage.twig', ['username' => $_SESSION['username']]);
    }

    public function endSession(): Redirect
    {
        Auth::logout();
        return new Redirect('/login');
    }
}

