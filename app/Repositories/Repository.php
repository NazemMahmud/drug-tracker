<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected $model;

    abstract public function setModel();

    public function __construct()
    {
        $this->setModel();
    }

    public function create(array $attributes): Model
    {
        return $this->model::create($attributes);
    }

    public function findBy(string $column, mixed $value): ?Model
    {
        // todo:
    }

    public function existsBy(array $conditions): bool
    {
        return $this->model->where($conditions)->exists();
    }
}
