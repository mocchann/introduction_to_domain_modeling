<?php

namespace UseCase;

use DomainObject\DomainService\UserService;
use DomainObject\Entity\User;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use Exception;
use Repositories\IUserRepository;
use UseCase\Command\UserRegisterCommand;

class UserRegisterService
{
    private readonly IUserRepository $user_repository;
    private readonly UserService $user_service;

    public function __construct(IUserRepository $user_repository, UserService $user_service)
    {
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
    }

    public function handle(UserRegisterCommand $command): void
    {
        $user_name = new UserName($command->getName());
        $mail_address = new UserMailAddress($command->getMailAddress());

        $user = new User($user_name, $mail_address);

        if ($this->user_service->exists($user)) throw new Exception('User already exists');

        $this->user_repository->save($user);
    }
}
