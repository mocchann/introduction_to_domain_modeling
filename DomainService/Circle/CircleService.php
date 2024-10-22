<?php

namespace DomainService\Circle;

use DomainObject\Entity\Circle\Circle;
use Repository\Circle\ICircleRepository;

class CircleService
{
    public function __construct(private readonly ICircleRepository $circleRepository)
    {
        $this->circleRepository = $circleRepository;
    }

    public function exists(Circle $circle): bool
    {
        return $this->circleRepository->findByName($circle->getName()) !== null;
    }
}
