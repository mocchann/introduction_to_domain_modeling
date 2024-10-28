<?php

namespace UseCase\Circle\Command;

class CircleCreateCommand
{
    public function __construct(private string $user_id, private string $name)
    {
        $this->user_id = $user_id;
        $this->name = $name;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
