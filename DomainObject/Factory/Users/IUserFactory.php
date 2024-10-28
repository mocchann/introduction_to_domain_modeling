<?php

namespace DomainObject\Factory\Users;

use DomainObject\Entity\Users\User;
use DomainObject\ValueObject\Users\UserMailAddress;
use DomainObject\ValueObject\Users\UserName;

interface IUserFactory
{
    public function create(UserName $name, UserMailAddress $mail_address): User;
}
