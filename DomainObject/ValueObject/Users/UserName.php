<?php

namespace DomainObject\ValueObject;

use Symfony\Component\Translation\Exception\InvalidArgumentException;

class UserName
{
    public function __construct(private string $value)
    {
        if (!$value) throw new InvalidArgumentException('Name is required');
        if (strlen($value) < 3) throw new InvalidArgumentException('Name must be at least 3 characters long');
        if (strlen($value) > 20) throw new InvalidArgumentException('Name must be at most 20 characters long');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
