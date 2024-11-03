<?php

namespace CleanArchitecture;

class StubUserGetInteractor implements IUserGetInputPort
{
    public function __construct(
        private readonly IUserGetPresenter $presenter
    ) {
        $this->presenter = $presenter;
    }

    public function handle(UserGetInputData $input_data): void
    {
        $user_data = new UserData("test-id", "test-name");
        $output_data = new UserUpdateOutputData($user_data);

        $this->presenter->output($output_data);
    }
}
