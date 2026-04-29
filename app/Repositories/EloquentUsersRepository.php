<?php

namespace App\Repositories;

use App\Models\Users;
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
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $attributes)
    {
        $attributes['created_at'] = now();
        $attributes['updated_at'] = now();

        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return null;
        }

        $attributes['updated_at'] = now();

        $record->update($attributes);

        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * LOGIN FUNCTION (FIXED FOR YOUR DB STRUCTURE)
     */
    public function login($userName, $password)
    {
        // find user by username
        $user = $this->model->where('UserName', $userName)->first();

        if (!$user) {
            return null;
        }

        /*
        =====================================================
        PASSWORD HANDLING (IMPORTANT)
        =====================================================
        Your DB password is currently:
        - either plain text OR MD5 (you showed MD5 hash earlier)

        So we support BOTH safely:
        */

        // OPTION 1: plain text match
        if ($user->Password === $password) {
            return $user;
        }

        // OPTION 2: MD5 match (if your DB uses MD5 like admin123)
        if ($user->Password === md5($password)) {
            return $user;
        }

        return null;
    }
}