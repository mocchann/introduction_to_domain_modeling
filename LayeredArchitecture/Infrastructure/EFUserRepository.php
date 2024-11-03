<?php

namespace Infrastructure;

use PDOException;

class EFUserRepository implements IUserRepository
{
    public function __construct(private readonly ItdddDbContext $context)
    {
        $this->context = $context;
    }

    public function findId(UserId $id): User
    {
        $target = $this->context->users->findId($id->getValue());
        if ($target === null) {
            throw new PDOException('User not found.');
        }

        return toModel($target);
    }

    public function findIds(IEnumerable $ids): array
    {
        $rawIds = $ids->select(function ($id) {
            return $id->getValue();
        });

        $targets = $this->context->users->where(function ($user) use ($rawIds) {
            return $rawIds->contains($user->id);
        });

        return $targets->select(function ($target) {
            return toModel($target);
        })->toArray();
    }

    public function findName(UserName $name): User
    {
        $target = $this->context->users->firstOrDefault(function ($user) use ($name) {
            return $user->name === $name->getValue();
        });
        if ($target === null) return null;

        return toModel($target);
    }

    // ...略
}
