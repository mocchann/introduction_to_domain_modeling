<?php

namespace DomainObject\DomainService\Users;

use DomainObject\Entity\Users\User;
use Repository\Users\IUserRepository;

class UserService
{
    public function __construct(private readonly IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function exists(User $user)
    {
        $duplicated_user = $this->user_repository->findName($user->getName());
        // ドメインサービスにドメインに関するルールを記述すれば、重複ルールの変更にも対応しやすい
        // $duplicated_user = $this->user_repository->findMail($user->getMailAddress());

        return $duplicated_user !== null;
    }
}
