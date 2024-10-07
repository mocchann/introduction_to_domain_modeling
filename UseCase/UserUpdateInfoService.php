<?php

namespace UseCase;

use DomainObject\DomainService\UserService;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use Exception;
use Repositories\IUserRepository;
use UseCase\Command\UserUpdateCommand;

class UserUpdateInfoService
{
    private readonly IUserRepository $user_repository;
    private readonly UserService $user_service;

    public function __construct(IUserRepository $user_repository, UserService $user_service)
    {
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
    }

    public function handle(UserUpdateCommand $command): void
    {
        $userId = new UserId($command->getId());
        $user = $this->user_repository->findId($userId);

        if ($user === null) throw new Exception('User not found');

        $name = $command->getName();
        if ($name !== null) {
            $new_user_name = new UserName($name);
            $user->changeName($new_user_name);

            if ($this->user_service->exists($user)) throw new Exception('User already exists');
        }

        $mail_address = $command->getMailAddress();
        if ($mail_address !== null) {
            $new_user_mail_address = new UserMailAddress($mail_address);
            $user->changeMailAddress($new_user_mail_address);

            if ($this->user_service->exists($user)) throw new Exception('User already exists');
        }

        $this->user_repository->save($user);
    }
}
