<?php


namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Lookup;
use App\Repositories\Interfaces\LookupRepositoryInterface;

class EloquentLookupRepository implements LookupRepositoryInterface
{
    protected $model;

    public function __construct(Lookup $model)
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
        $attributes['LookupID'] = (string) Str::uuid();
        $attributes['LookupUpdateDate'] = now();   
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
