<?php

namespace UseCase;

use DomainObject\DomainService\UserService;
use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use DTO\UserData;
use Exception;
use Repository\IUserRepository;
use UseCase\Command\UserDeleteCommand;
use UseCase\Command\UserUpdateCommand;

class SampleUserApplicationService
{
    private readonly IUserRepository $user_repository;
    private readonly UserService $user_service;

    public function __construct(IUserRepository $user_repository, UserService $user_service)
    {
        $this->user_repository = $user_repository;
        $this->user_service = $user_service;
    }

    public function register(string $id, string $name, string $mail_address): void
    {
        $user = new User(new UserId($id), new UserName($name), new UserMailAddress($mail_address));

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

    public function update(UserUpdateCommand $command): void
    {
        $target_id = new UserId($command->getId());
        $user = $this->user_repository->findId($target_id);

        if ($user === null) throw new Exception('User not found');

        // メールアドレスだけを更新するため、ユーザー名が指定されないことを考慮
        $name = $command->getName();
        if ($name !== null) {
            $new_user_name = new UserName($name);
            $user->changeName($new_user_name);

            if ($this->user_service->exists($user)) throw new Exception('User already exists');
        }

        // メールアドレスを更新できるように
        $mail_address = $command->getMailAddress();
        if ($mail_address !== null) {
            $new_mail_address = new UserMailAddress($mail_address);
            $user->changeMailAddress($new_mail_address);
        }

        $this->user_repository->save($user);
    }

    public function delete(UserDeleteCommand $command): void
    {
        $target_id = new UserId($command->getId());
        $user = $this->user_repository->findId($target_id);

        if ($user === null) throw new Exception('User not found');
        // return; ユーザーが見つからないときは退会成功とする判断もある

        $this->user_repository->delete($user);
    }
}

// コマンドオブジェクトを利用してアプリケーションサービスの制御を行う

// ユーザー名変更だけ行う場合
$update_name_command = new UserUpdateCommand($id, $name = "nrs");

$user_application_service->update($update_name_command);

// メールアドレス変更だけ行う場合
$update_mail_address_command = new UserUpdateCommand($id, null, $mail_address = "xxxx@example.com");
$user_application_service->update($update_mail_address_command);
