<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrdersRepositoryInterface;

class EloquentOrdersRepository implements OrdersRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
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
        $attributes['OrderID'] = (string) Str::uuid();
        $attributes['OrderDate'] = now();  
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
