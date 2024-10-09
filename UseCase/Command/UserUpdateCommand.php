<?php

namespace UseCase\Command;

class UserUpdateCommand
{
    private string $id;
    private string $name;
    private string $mail_address;

    public function __construct(string $id, string $name = null, string $mail_address = null)
    {
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
