<?php

namespace DomainObject\ValueObject\Circle;

use Symfony\Component\Translation\Exception\InvalidArgumentException;

class CircleName
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

    public function equals(CircleName $other): bool
    {
        if ($other === null) return false;
        if ($this === $other) return true;

        return $this->value === $other->getValue();
    }

    public function getHashCode(): int
    {
        return $this->value !== null ? crc32($this->value) : 0;
    }
}
