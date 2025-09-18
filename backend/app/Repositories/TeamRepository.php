<?php
namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository implements TeamRepositoryInterface {
    protected $model;

    public function __construct()
    {
        $this->model = new Team();
    }
    public function all() : Collection {
        return $this->model->all();
    }
}
