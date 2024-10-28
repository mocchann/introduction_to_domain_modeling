<?php

namespace UseCase\Users;

use UseCase\Command\UserRegisterCommand;

interface IUserRegisterService
{
    public function handle(UserRegisterCommand $command): void;
}
