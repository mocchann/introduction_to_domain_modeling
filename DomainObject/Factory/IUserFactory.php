<?php

namespace DomainObject\Factory;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserName;

interface IUserFactory
{
    public function create(UserName $name): User;
}
