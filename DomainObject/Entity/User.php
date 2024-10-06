<?php

namespace DomainObject\Entity;

use DomainObject\ValueObject\UserId;
use DomainObject\ValueObject\UserMailAddress;
use DomainObject\ValueObject\UserName;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class User
{
    private $id;
    private $name;
    private $mail_address;

    public function __construct(UserName $name, UserMailAddress $mail_address)
    {
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $this->id = new UserId(uniqid());
        $this->name = $name;
        $this->mail_address = $mail_address;
    }

    public static function reconstruct(UserId $id, UserName $name, UserMailAddress $mail_address): User
    {
        if ($id === null) throw new InvalidArgumentException('Id is required');
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $user = new self($name, $mail_address);
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

    public function getMailAddress(): UserMailAddress
    {
        return $this->mail_address;
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
