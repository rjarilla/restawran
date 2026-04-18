<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * Get all records.
     *
     * @return mixed
     */
    public function all();

    /**
     * Find a record by its primary key.
     *
     * @param mixed $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create a new record.
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * Update a record by its primary key.
     *
     * @param mixed $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, array $attributes);

    /**
     * Delete a record by its primary key.
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id);
}
