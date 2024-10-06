<?php

namespace UseCase;

use DomainObject\DomainService\UserService;
use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use DTO\UserData;
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

    public function register(string $name, string $mail_address): void
    {
        $user = new User(new UserName($name), new UserMailAddress($mail_address));

        if ($this->user_service->exists($user)) throw new Exception('User already exists');

        $this->user_repository->save($user);
    }

    public function get(string $user_id): UserData
    {
        $target_id = new UserId($user_id);
        $user = $this->user_repository->findId($target_id);

        if ($user === null) throw new Exception('User not found');

        // アプリケーションサービス以外からchangeNameメソッドを呼び出されるのを防ぐため、DTOに変換して返す
        return new UserData($user);
    }

    public function update(string $user_id, string $name = null, string $mail_address = null): void
    {
        $target_id = new UserId($user_id);
        $user = $this->user_repository->findId($target_id);

        if ($user === null) throw new Exception('User not found');

        // メールアドレスだけを更新するため、ユーザー名が指定されないことを考慮
        if ($name !== null) {
            $new_user_name = new UserName($name);
            $user->changeName($new_user_name);

            if ($this->user_service->exists($user)) throw new Exception('User already exists');
        }

        // メールアドレスを更新できるように
        if ($mail_address !== null) {
            $new_mail_address = new UserMailAddress($mail_address);
            $user->changeMailAddress($new_mail_address);
        }

        $this->user_repository->save($user);
    }
}
