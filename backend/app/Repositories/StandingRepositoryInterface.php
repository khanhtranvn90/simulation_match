<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Standing;

interface StandingRepositoryInterface extends BaseRepositoryInterface
{
    public function with($relations): Collection;
    public function max($column): int;
    public function getByCriteria(array $criteria, $relations = []): Collection;
}
