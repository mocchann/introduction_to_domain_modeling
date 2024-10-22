<?php

namespace Repository\Circle;

use DomainObject\Entity\Circle\Circle;
use DomainObject\ValueObject\Circle\CircleId;
use DomainObject\ValueObject\Circle\CircleName;

interface ICircleRepository
{
    public function save(Circle $circle): void;
    public function findById(CircleId $id): Circle;
    public function findByName(CircleName $name): Circle;
}
