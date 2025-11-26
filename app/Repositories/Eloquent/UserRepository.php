<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

final class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::query()->where('phone', $phone)->first();
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): User
    {
        /** @var User */
        $user = User::query()->create($data);

        return $user;
    }

    public function updateStatus(int $userId, string $status): bool
    {
        return (bool) User::query()->where('id', $userId)->update(['status' => $status]);
    }
}
