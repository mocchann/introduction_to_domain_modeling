<?php

namespace UseCase\Command;

class UserRegisterCommand
{
    private string $name;
    private string $mail_address;

    public function __construct(string $name, string $mail_address)
    {
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
