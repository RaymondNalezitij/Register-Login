<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\LoginService;
use App\Services\LoginServiceRequest;
use App\View;

class LoginController
{

    private LoginService $loginService;

    public function __construct(LoginService $loginService)
    {

        $this->loginService = $loginService;
    }

    public function index()
    {
        if (Auth::isAuthorized()) {
            return new Redirect('/');
        }
        return new View('Login.twig');
    }

    public function validate(): Redirect
    {
        $this->loginService->execute(
            new LoginServiceRequest(
                $_POST['email'],
                $_POST['password'],
            )
        );

        return new Redirect('/');
    }
}