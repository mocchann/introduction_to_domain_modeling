<?php

namespace UseCase\Circle;

use DomainObject\Factory\Circle\ICircleFactory;
use DomainObject\ValueObject\Circle\CircleName;
use DomainObject\ValueObject\Users\UserId;
use DomainService\Circle\CircleService;
use Exception;
use PDO;
use Repository\Circle\ICircleRepository;
use Repository\Users\IUserRepository;
use UseCase\Circle\Command\CircleCreateCommand;

class CircleApplicationService
{
    public function __construct(
        private readonly ICircleFactory $circle_factory,
        private readonly ICircleRepository $circle_repository,
        private readonly CircleService $circle_service,
        private readonly IUserRepository $user_repository,
        private readonly PDO $pdo
    ) {
        $this->circle_factory = $circle_factory;
        $this->circle_repository = $circle_repository;
        $this->circle_service = $circle_service;
        $this->user_repository = $user_repository;
        $this->pdo = $pdo;
    }

    public function create(CircleCreateCommand $command): void
    {
        try {
            $this->pdo->beginTransaction();

            $ownerId = new UserId($command->getUserId());
            $owner = $this->user_repository->findId($ownerId);
            if ($owner === null) {
                throw new Exception('User not found');
            }

            $name = new CircleName($command->getName());
            $circle = $this->circle_factory->create($name, $owner);
            if ($this->circle_service->exists($circle)) {
                throw new Exception('Circle already exists');
            }
            $this->circle_repository->save($circle);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
