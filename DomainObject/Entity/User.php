<?php

namespace DomainObject\Entity;

use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserName;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class User
{
    private $id;
    private $name;

    public function __construct(UserName $name)
    {
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $this->id = new UserId(uniqid());
        $this->name = $name;
    }

    public static function reconstruct(UserId $id, UserName $name): User
    {
        if ($id === null) throw new InvalidArgumentException('Id is required');
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $user = new self($name);
        $user->id = $id;
        return $user;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    private function setName(UserName $name): void
    {
        $this->name = $name;
    }

    public function changeName(UserName $name): void
    {
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $this->setName($name);
    }
}
