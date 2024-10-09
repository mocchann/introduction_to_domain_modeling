<?php

namespace UseCase;

use UseCase\Command\UserRegisterCommand;

class MockRegisterService implements IUserRegisterService
{
    public function handle(UserRegisterCommand $command): void
    {
        // nop
    }
}
