<?php

namespace DomainObject\Factory\Users;

use DomainObject\Entity\Users\User;
use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserMailAddress;
use DomainObject\ValueObject\Users\UserName;

class InMemoryUserFactory implements IUserFactory
{
    // 現在のID
    private int $current_id;

    public function create(UserName $name, UserMailAddress $mail_address): User
    {
        // ユーザー生成のたびにインクリメント
        $this->current_id++;

        return new User(new UserId((string)$this->current_id), $name, $mail_address);
    }
}
