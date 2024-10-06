<?php

namespace DomainObject\ValueObject;

use Symfony\Component\Translation\Exception\InvalidArgumentException;

class UserMailAddress
{
    private $value;

    public function __construct(string $value)
    {
        if (!$value) throw new InvalidArgumentException('Mail Address is required');

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
