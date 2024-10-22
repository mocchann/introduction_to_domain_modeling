<?php

namespace Repository\Users;

use DomainObject\Entity\Users\User;
use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserName;

interface IUserRepository
{
    public function findId(UserId $id): User;
    public function findName(UserName $name): User;
    public function save(User $user): void;
    public function delete(User $user): void;
}
