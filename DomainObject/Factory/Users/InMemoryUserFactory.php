<?php

namespace DomainObject\Factory;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;

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
