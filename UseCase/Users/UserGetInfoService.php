<?php

namespace UseCase;

use DomainObject\ValueObject\UserId;
use DTO\UserData;
use Exception;
use Repository\IUserRepository;

class UserGetInfoService
{
    public function __construct(private readonly IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function get(string $user_id): UserData
    {
        $userId = new UserId($user_id);
        $user = $this->user_repository->findId($userId);

        if ($user === null) throw new Exception('User not found');

        return new UserData($user);
    }
}
