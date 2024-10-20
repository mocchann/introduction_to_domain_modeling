<?php

namespace UseCase\Command;

class UserDeleteCommand
{
    public function __construct(private string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
