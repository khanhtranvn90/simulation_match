<?php
namespace App\Repositories;

use App\Models\Standing;
use Illuminate\Database\Eloquent\Collection;

class StandingRepository implements StandingRepositoryInterface {
    protected $model;

    public function __construct()
    {
        $this->model = new Standing();
    }

    public function find($id) {
        return $this->model->find($id);
    }

    public function all(): Collection {
        return $this->model->all();
    }

    public function max($column): int
    {
        return $this->model->max($column);
    }

    public function with($relations): Collection
    {
        return $this->model->with($relations);
    }

    public function getByCriteria(array $criteria, $relations = []): Collection
    {
        $query = $this->model->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }
}
