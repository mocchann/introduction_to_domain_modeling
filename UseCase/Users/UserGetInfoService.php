<?php

namespace UseCase;

use DomainObject\ValueObject\UserId;
use DTO\UserData;
use Exception;
use Repositories\IUserRepository;

class UserGetInfoService
{
    private readonly IUserRepository $user_repository;

    public function __construct(IUserRepository $user_repository)
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
