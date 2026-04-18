<?php

namespace App\Repositories;

use App\Models\Users;
use App\Models\UserProfile;
use App\Models\UserProfPrivileges;
use App\Repositories\Interfaces\UsersRepositoryInterface;

class EloquentUsersRepository implements UsersRepositoryInterface
{
    protected $model;

    public function __construct(Users $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        // Eloquent: eager load relationships
        return $this->model->all()->load(['userProfile', 'userProfilePrivileges']);
    }

    public function find($id)
    {
        // Eloquent: eager load relationships for a single model
        $user = $this->model->find($id);
        if ($user) {
            $user->load(['userProfile', 'userProfilePrivileges']);
        }
        return $user;
    }

    public function create(array $attributes)
    {
        $attributes['UserID'] = (string) Str::uuid();
        $attributes['UserUpdateDate'] = now();
        if (isset($attributes['UserPassword'])) {
            $attributes['UserPassword'] = md5($attributes['UserPassword']);
        }
        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->model->find($id);
        if ($record) {
            if (isset($attributes['UserPassword'])) {
                $attributes['UserPassword'] = md5($attributes['UserPassword']);
            }
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

    /**
     * Login function: returns user if credentials match, null otherwise
     */
    public function login($userName, $password)
    {
        $user = $this->model->where('UserName', $userName)->first();
        if ($user && $user->UserPassword === md5($password)) {
            $user->load(['userProfile', 'userProfilePrivileges']);
            return $user;
        }
        return null;
    }
}
