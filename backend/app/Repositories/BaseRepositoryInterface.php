<?php
namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function getByCriteria(array $criteria, $relations = []);
}
