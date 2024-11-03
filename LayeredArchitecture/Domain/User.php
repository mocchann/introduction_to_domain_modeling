<?php

namespace Domain;

class User
{
    public function __construct(public UserId $id, public UserName $name, public UserType $type)
    {
        if ($id === null) throw new \InvalidArgumentException('id is required');
        if ($name === null) throw new \InvalidArgumentException('name is required');

        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
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

    public function getType(): UserType
    {
        return $this->type;
    }

    private function setType(UserType $type): void
    {
        $this->type = $type;
    }

    public function changeName(UserName $name): void
    {
        if ($name === null) throw new \InvalidArgumentException('name is required');

        $this->setName($name);
    }

    public function upgrade(): void
    {
        $this->setType(new UserType("premium"));
    }

    public function downgrade(): void
    {
        $this->setType(new UserType("normal"));
    }
}
