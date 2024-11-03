<?php

namespace CleanArchitecture;

interface IUserGetInputPort
{
    public function handle(UserGetInputData $input_data): void;
}
