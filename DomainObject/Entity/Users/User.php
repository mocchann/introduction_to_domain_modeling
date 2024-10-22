<?php

namespace DomainObject\Entity\Users;

use DomainObject\ValueObject\Users\UserId;
use DomainObject\ValueObject\Users\UserMailAddress;
use DomainObject\ValueObject\Users\UserName;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class User
{
    public function __construct(
        private UserId $id,
        private UserName $name,
        private UserMailAddress $mail_address
    ) {
        if ($id === null) throw new InvalidArgumentException('Id is required');
        if ($name === null) throw new InvalidArgumentException('Name is required');
        if ($mail_address === null) throw new InvalidArgumentException('Mail address is required');

        $this->id = $id;
        $this->name = $name;
        $this->mail_address = $mail_address;
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

    private function setMailAddress(UserMailAddress $mail_address): void
    {
        $this->mail_address = $mail_address;
    }

    public function changeName(UserName $name): void
    {
        if ($name === null) throw new InvalidArgumentException('Name is required');

        $this->setName($name);
    }

    public function changeMailAddress(UserMailAddress $mail_address): void
    {
        if ($mail_address === null) throw new InvalidArgumentException('Mail address is required');

        $this->setMailAddress($mail_address);
    }
}
