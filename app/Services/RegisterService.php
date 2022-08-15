<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class RegisterService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {

        $this->userRepository = $userRepository;
    }

    public function execute(RegisterServiceRequest $request)
    {
        $user = new User(
            $request->getUsername(),
            $request->getEmail(),
            $request->getPassword(),
        );

        $this->userRepository->record($user);

        return $user;
    }

    public function checkEmail(string $email): bool
    {
        return $this->userRepository->checkEmail($email);
    }
}