<?php

namespace App\Repositories;

use App\Models\ProductInventory;
use App\Repositories\Interfaces\ProductInventoryRepositoryInterface;
use Illuminate\Support\Str;

class EloquentProductInventoryRepository implements ProductInventoryRepositoryInterface
{
    protected $model;

    public function __construct(ProductInventory $model)
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
        $attributes['ProductBatchID'] = (string) Str::uuid();
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
