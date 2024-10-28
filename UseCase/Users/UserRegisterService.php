<?php

namespace UseCase\Users;

use DomainObject\DomainService\Users\UserService;
use DomainObject\Factory\Users\IUserFactory;
use DomainObject\ValueObject\Users\UserMailAddress;
use DomainObject\ValueObject\Users\UserName;
use Exception;
use PDO;
use PDOException;
use Repository\Users\IUserRepository;
use UseCase\Command\UserRegisterCommand;

class UserRegisterService implements IUserRegisterService
{
    public function __construct(
        private readonly PDO $connection,
        private readonly UserService $user_service,
        private readonly IUserFactory $user_factory,
        private readonly IUserRepository $user_repository
    ) {
        $this->connection = $connection;
        $this->user_service = $user_service;
        $this->user_factory = $user_factory;
        $this->user_repository = $user_repository;
    }

    public function handle(UserRegisterCommand $command): void
    {
        try {
            $this->connection->beginTransaction();

            $user_name = new UserName($command->getName());
            $mail_address = new UserMailAddress($command->getMailAddress());

            $user = $this->user_factory->create($user_name, $mail_address);

            if ($this->user_service->exists($user)) throw new Exception('User already exists');

            $this->user_repository->save($user);

            $this->connection->commit();
        } catch (PDOException $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
