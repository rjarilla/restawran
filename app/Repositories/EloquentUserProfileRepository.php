<?php

namespace App\Repositories;

use App\Models\UserProfile;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use Illuminate\Support\Str;

class EloquentUserProfileRepository implements UserProfileRepositoryInterface
{
    protected $model;

    public function __construct(UserProfile $model)
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
        $attributes['UserProfileID'] = (string) Str::uuid();
        $attributes['UserProfileUpdateDate'] = now();
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
}
