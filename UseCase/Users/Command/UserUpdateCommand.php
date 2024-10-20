<?php

namespace UseCase\Command;

class UserUpdateCommand
{
    public function __construct(
        private string $id,
        private string $name = null,
        private string $mail_address = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->mail_address = $mail_address;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMailAddress(): string
    {
        return $this->mail_address;
    }
}
