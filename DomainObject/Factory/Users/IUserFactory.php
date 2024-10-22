<?php

namespace DomainObject\Factory;

use DomainObject\Entity\User;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;

interface IUserFactory
{
    public function create(UserName $name, UserMailAddress $mail_address): User;
}
