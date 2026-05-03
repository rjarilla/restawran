<?php

namespace App\Repositories;

use App\Models\Users;
use App\Repositories\Interfaces\UsersRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


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
        $attributes['UserUpdateDate'] = now();

        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return null;
        }

        $attributes['UserUpdateDate'] = now();

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
     * LOGIN FUNCTION
     */
    public function login($userName, $password)
    {
        // find user by username
        $user = $this->model->where('UserName', $userName)->first();
        if ($user && Hash::check($password, $user->Password)) {
            // $user->load(['userprofile', 'userprofileprivileges']);
            return $user;
        }
        return null;
    }
}
