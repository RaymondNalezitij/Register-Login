<?php

namespace App\Repositories;

use App\Models\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UserInfoRepositoryMYSQL implements UserRepositoryInterface
{

    private Connection $connection;

    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'user_credentials',
            'user' => 'user',
            'password' => 'password',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        ];
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams);
    }

    public function record(User $user): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->insert('user_info')
            ->values([
                'username' => ':username',
                'email' => ':email',
                'password' => ':password',
            ])
            ->setParameters([
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
            ])
            ->executeQuery();

        $user->setId($this->connection->lastInsertId());
    }

    public function checkEmail(string $email): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $user = $queryBuilder
            ->select('email')
            ->from('user_info')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->fetchAssociative();

        if (!$user) {
            return false;
        } else {
            return true;
        }

    }

    public function findByEmail(string $email): User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $user = $queryBuilder
            ->select('*')
            ->from('user_info')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->fetchAssociative();

        if ($user['username'] == NULL) {
            error_log("WRONG EMAIL!");
            header('Location: /login');
            die;
        }

        return new User(
            $user['username'],
            $user['email'],
            $user['password'],
            $user['id']
        );
    }
}