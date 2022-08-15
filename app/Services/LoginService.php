<?php

namespace App\Services;

use App\Repositories\UserInfoRepositoryMYSQL;

class LoginService
{

    private UserInfoRepositoryMYSQL $userInfoRepository;

    public function __construct(UserInfoRepositoryMYSQL $userInfoRepository)
    {
        $this->userInfoRepository = $userInfoRepository;
    }

    public function execute(LoginServiceRequest $request): void
    {
        $user = $this->userInfoRepository->findByEmail($request->getEmail());

        if (!password_verify($request->getPassword(), $user->getPassword())) {
            error_log("WRONG PASSWORD");
        } else {
            header('Location: /');
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['auth_id'] = $user->getId();
        }
    }
}

