<?php

namespace UseCase;

use DomainObject\DomainService\UserService;
use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserName;
use Exception;
use Repositories\IUserRepository;

class UserApplicationService
{
    private readonly IUserRepository $user_repository;
    private readonly UserService $user_service;

    public function __construct(IUserRepository $user_repository, UserService $user_service)
    {
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
    }

    public function register(string $name): void
    {
        $user = new User(new UserName($name));

        if ($this->user_service->exists($user)) throw new Exception('User already exists');

        $this->user_repository->save($user);
    }

    public function get(string $user_id): User
    {
        $target_id = new UserId($user_id);
        $user = $this->user_repository->findId($target_id);

        return $user;
    }
}
