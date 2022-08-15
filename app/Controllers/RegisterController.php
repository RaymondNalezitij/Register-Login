<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\RegisterService;
use App\Services\RegisterServiceRequest;
use App\View;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Rules;

class RegisterController
{

    private RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function register()
    {
        if (Auth::isAuthorized()) {
            return new Redirect('/');
        }
        return new View('Register.twig');
    }

    public function store()
    {
        // validation
        $validator = new Rules\KeySet(
            new Rules\Key('username', new Rules\AllOf(
                new Rules\NoWhitespace(),
                new Rules\Length(3, 15)
            )),
            new Rules\Key('email', new Rules\AllOf(
                new Rules\Email(),
                new Rules\NoWhitespace(),
                new Rules\Length(1, 20)
            )),
            new Rules\Key('password', new Rules\AllOf(
                new Rules\NoWhitespace(),
                new Rules\Length(4)
            )),
            new Rules\Key('confirmPassword', new Rules\AllOf(
                new Rules\Equals($_POST['password']),
            ))
        );

        try {
            $validator->assert($_POST);

            if ($this->registerService->checkEmail($_POST['email'])) {
                $_SESSION['errors']['email'] = "Email already taken";

                return new Redirect('/register');
            } else {
                $user = $this->registerService->execute(
                    new RegisterServiceRequest(
                        $_POST['username'],
                        $_POST['email'],
                        $_POST['password'],
                    )
                );

                Auth::authorize($user->getId());

                return new Redirect('/');
            }

        } catch (NestedValidationException $exception) {
            $_SESSION['errors'] = $exception->getMessages();
            return new Redirect('/register');
        }
    }
}