<?php

namespace LayeredArchitecture\Application;

use Exception;
use PDO;

class UserApplicationService
{
    public function __construct(
        private readonly IUserFactory $user_factory,
        private readonly IUserRepository $user_repository,
        private readonly UserService $user_service,
        private readonly PDO $pdo
    ) {
        $this->user_factory = $user_factory;
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
        $this->pdo = $pdo;
    }

    public function get(UserGetCommand $command): UserGetResult
    {
        $id = new UserId($command->getId());
        $user = $this->user_repository->findId($id);
        if ($user === null) {
            throw new Exception('User not found');
        }

        $data = new UserData($user);

        return new UserGetResult($data);
    }

    public function getAll(): UserGetAllResult
    {
        $users = $this->user_repository->findAll();
        $user_models = $users->select(function ($user) {
            return (new UserData($user))->toArray();
        });

        return new UserGetAllResult($user_models);
    }

    public function register(UserRegisterCommand $command): UserRegisterResult
    {
        $this->pdo->beginTransaction();
        $name = new UserName($command->getName());
        $user = $this->user_factory->create($name);
        if ($this->user_service->exists($user)) {
            throw new Exception('User already exists');
        }

        $this->user_repository->save($user);

        $this->pdo->commit();

        return new UserRegisterResult($user->getId()->getValue());
    }

    public function update(UserUpdateCommand $command): void
    {
        $this->pdo->beginTransaction();

        $id = new UserId($command->getId());
        $user = $this->user_repository->findId($id);
        if ($user === null) {
            throw new Exception('User not found');
        }

        if ($command->getName() !== null) {
            $name = new UserName($command->getName());
            $user->changeName($name);
            if ($this->user_service->exists($user)) {
                throw new Exception('User already exists');
            }
        }

        $this->user_repository->save($user);

        $this->pdo->commit();
    }

    public function delete(UserDeleteCommand $command): void
    {
        $this->pdo->beginTransaction();

        $id = new UserId($command->getId());
        $user = $this->user_repository->findId($id);
        if ($user === null) {
            return;
        }

        $this->user_repository->delete($user);

        $this->pdo->commit();
    }
}
