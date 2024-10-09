<?php

use UseCase\Command\UserRegisterCommand;
use UseCase\IUserRegisterService;

class Client
{
    private IUserRegisterService $user_register_service;

    // ...略

    public function register(string $name, string $mail_address): void
    {
        $command = new UserRegisterCommand($name, $mail_address);
        $this->user_register_service->handle($command);
    }
}
