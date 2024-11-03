<?php

namespace Domain;

class UserService
{
    public function __construct(private readonly IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function exists(User $user): bool
    {
        $duplicated_user = $this->user_repository->findName($user->getName());

        return $duplicated_user !== null;
    }
}
