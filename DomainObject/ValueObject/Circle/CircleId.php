<?php

namespace DomainObject\ValueObject\Circle;

use Symfony\Component\Translation\Exception\InvalidArgumentException;

class CircleId
{
    public function __construct(private string $value)
    {
        if (!$value) throw new InvalidArgumentException('Id is required');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
