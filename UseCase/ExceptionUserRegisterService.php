<?php

namespace UseCase;

use Exception;
use UseCase\Command\UserRegisterCommand;

class ExceptionUserRegisterService implements IUserRegisterService
{
    public function handle(UserRegisterCommand $command): void
    {
        throw new Exception('UserRegisterService is not implemented');
    }
}
