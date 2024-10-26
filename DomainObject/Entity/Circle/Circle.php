<?php

namespace DomainObject\Entity\Circle;

use DomainObject\ValueObject\Circle\CircleId;
use DomainObject\ValueObject\Circle\CircleName;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use DomainObject\Entity\Users\User;

class Circle
{
    public function __construct(
        private CircleId $id,
        private CircleName $name,
        private User $owner,
        private array $members,
    ) {
        if ($id === null) throw new InvalidArgumentException('Id is required');
        if ($name === null) throw new InvalidArgumentException('Name is required');
        if ($owner === null) throw new InvalidArgumentException('Owner is required');
        if ($members === null) throw new InvalidArgumentException('Members is required');

        $this->id = $id;
        $this->name = $name;
        $this->owner = $owner;
        $this->members = $members;
    }

    public function getId(): CircleId
    {
        return $this->id;
    }

    public function getName(): CircleName
    {
        return $this->name;
    }

    private function setName(CircleName $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    private function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getMembers(): array
    {
        return $this->members;
    }

    private function addMember(User $member): void
    {
        $this->members[] = $member;
    }
}
