<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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

    //
    public function findBy(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    public function existsBy(array $conditions): bool
    {
        return $this->model->where($conditions)->exists();
    }

    public function getAllBy(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }

    public function deleteBy(array $conditions): bool
    {
        $record = $this->findBy($conditions);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function updateBy(array $conditions, array $data): bool
    {
        return $this->model->where($conditions)->update($data);
    }
}
