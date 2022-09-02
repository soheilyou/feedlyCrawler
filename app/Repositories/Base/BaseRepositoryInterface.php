<?php

namespace App\Repositories\Base;

interface BaseRepositoryInterface
{
    public function create(array $attributes);

    public function update(array $attributes): bool;

    public function all($filters = [], $columns = ["*"]);

    public function find($id);

    public function findOrFail($id);

    public function findBy(array $data);

    public function findOneBy(array $data);

    public function findOneByOrFail(array $data);
}
