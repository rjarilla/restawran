<?php

namespace App\Repositories;

use App\Models\OrderDetails;
use App\Repositories\Interfaces\OrderDetailsRepositoryInterface;

class EloquentOrderDetailsRepository implements OrderDetailsRepositoryInterface
{
    protected $model;

    public function __construct(OrderDetails $model)
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
        $attributes['OrderDetailsID'] = (string) Str::uuid();
        $attributes['OrderDetailsUpdateDate'] = now();  
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
