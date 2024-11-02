<?php

namespace LayeredArchitecture\Controllers;

class UserController extends Controller
{
    public function __construct(private readonly UserApplicationService $user_application_service)
    {
        $this->user_application_service = $user_application_service;
    }

    public function index(): UserIndexResponseModel
    {
        $result = $this->user_application_service->getAll();
        $users = $result->users->select(function ($user) {
            return (new UserResponseModel($user->id, $user->name))->toArray();
        });

        return new UserIndexResponseModel($users);
    }

    public function get(string $id): UserGetResponseModel
    {
        $command = new UserGetCommand($id);
        $result = $this->user_application_service->get($command);

        $user_model = new UserResponseModel($result->user);

        return new UserGetResponseModel($user_model);
    }

    public function post(UserPostRequestModel $request): UserPostResponseModel
    {
        $command = new UserRegisterCommand($request->user_name);
        $result = $this->user_application_service->register($command);

        return new UserPostResponseModel($result->createdUserId());
    }

    public function put(string $id, UserPutRequestModel $request): void
    {
        $command = new UserUpdateCommand($id, $request->name);
        $this->user_application_service->update($command);
    }

    public function delete(string $id): void
    {
        $command = new UserDeleteCommand($id);
        $this->user_application_service->delete($command);
    }
}
