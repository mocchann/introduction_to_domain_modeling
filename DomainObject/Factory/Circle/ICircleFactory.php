<?php

namespace DomainObject\Factory\Circle;

use DomainObject\Entity\Circle\Circle;
use DomainObject\Entity\User;
use DomainObject\ValueObject\Circle\CircleName;

interface ICircleFactory
{
    public function create(CircleName $name, User $owner): Circle;
}
