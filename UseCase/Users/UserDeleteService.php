<?php

namespace UseCase;

use DomainObject\ValueObject\UserId;
use Exception;
use Repository\IUserRepository;
use UseCase\Command\UserDeleteCommand;

class UserDeleteService
{
    public function __construct(private readonly IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function handle(UserDeleteCommand $command)
    {
        $userId = new UserId($command->getId());
        $user = $this->user_repository->findId($userId);

        if ($user === null) throw new Exception('User not found');

        $this->user_repository->delete($user);
    }
}
