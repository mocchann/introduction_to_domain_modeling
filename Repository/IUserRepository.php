<?php

namespace Repositories;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserName;

interface IUserRepository
{
    public function findId(UserId $id): User;
    public function findName(UserName $name): User;
    public function save(User $user): void;
    public function delete(User $user): void;
}
