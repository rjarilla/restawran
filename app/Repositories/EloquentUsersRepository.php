<?php

namespace App\Repositories;

use Illuminate\Support\Str;
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
        return $this->model->all()->load(['userProfile', 'userProfilePrivileges']);
    }

    public function find($id)
    {
        $user = $this->model->find($id);

        if ($user) {
            $user->load(['userprofile', 'userprofileprivileges']);
        }

        return $user;
    }

    public function create(array $attributes)
    {
        $attributes['UserID'] = (string) Str::uuid();
        $attributes['UserUpdateDate'] = now();

        if (isset($attributes['UserPassword'])) {
            $attributes['Password'] = $attributes['UserPassword'];
            unset($attributes['UserPassword']);
        }

        return $this->model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $record = $this->model->find($id);

        if ($record) {
            if (isset($attributes['UserPassword'])) {
                $attributes['Password'] = $attributes['UserPassword'];
                unset($attributes['UserPassword']);
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
     * FINAL LOGIN FIX (NO CRASH VERSION)
     */
    public function login($userName, $password)
    {
        $user = $this->model->where('UserName', $userName)->first();

        if (!$user) {
            return null;
        }

        // Plain text password check (matches your DB)
        if ($user->Password === $password) {
            return $user; // IMPORTANT: no relationships to avoid crash
        }

        return null;
    }
}