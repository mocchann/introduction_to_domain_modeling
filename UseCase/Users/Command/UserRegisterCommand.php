<?php

namespace UseCase\Command;

class UserRegisterCommand
{
    public function __construct(
        private string $name,
        private string $mail_address
    ) {
        $this->name = $name;
        $this->mail_address = $mail_address;
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
