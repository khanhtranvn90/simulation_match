<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Standing;

interface TeamRepositoryInterface
{
    public function all(): Collection;
}
