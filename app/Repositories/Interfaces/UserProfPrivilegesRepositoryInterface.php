<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Models\UserProfPrivileges;

interface UserProfPrivilegesRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCompositeKey(string $profile, string $privilege): ?UserProfPrivileges;

    public function updateByCompositeKey(string $profile, string $privilege, array $attributes): bool;

    public function deleteByCompositeKey(string $profile, string $privilege): bool;
}
