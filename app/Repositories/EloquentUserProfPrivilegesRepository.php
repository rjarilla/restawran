<?php

namespace App\Repositories;

use App\Models\UserProfPrivileges;
use App\Repositories\Interfaces\UserProfPrivilegesRepositoryInterface;
use Illuminate\Support\Str;

class EloquentUserProfPrivilegesRepository implements UserProfPrivilegesRepositoryInterface
{
    protected $model;

    public function __construct(UserProfPrivileges $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        $attributes['UserProfPrivilegesID'] = (string) Str::uuid();
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->update($attributes);
            return $record;
        }
        return null;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
    public function findByCompositeKey(string $profile, string $privilege): ?UserProfPrivileges
    {
        return $this->model
            ->where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->first();
    }

    public function updateByCompositeKey(string $profile, string $privilege, array $attributes): bool
    {
        return (bool) $this->model
            ->where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->update($attributes);
    }

    public function deleteByCompositeKey(string $profile, string $privilege): bool
    {
        return (bool) $this->model
            ->where('UserProfileID', $profile)
            ->where('UserPrivilegesID', $privilege)
            ->delete();
    }
}
