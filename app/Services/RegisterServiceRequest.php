<?php

namespace App\Services;

class RegisterServiceRequest
{
    private string $username;
    private string $email;
    private string $password;

    public function __construct(string $username, string $email, string $password)
    {

        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}