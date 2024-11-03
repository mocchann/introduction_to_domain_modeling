<?php

namespace CleanArchitecture;

class UserGetInteractor implements IUserGetInputPort
{
    public function __construct(
        private readonly IUserRepository $user_repository,
        private readonly IUserGetPresenter $presenter
    ) {
        $this->user_repository = $user_repository;
        $this->presenter = $presenter;
    }

    public function handle(UserGetInputData $input_data): void
    {
        $targetId = new UserId($input_data->getId());
        $user = $this->user_repository->findId($targetId);

        $user_data = new UserData($user->getId(), $user->getName());
        $output_data = new UserUpdateOutputData($user_data);
        $this->presenter->output($output_data);
    }
}
