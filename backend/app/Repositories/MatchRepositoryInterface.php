<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Collection;

interface MatchRepositoryInterface extends BaseRepositoryInterface
{
    public function with($relations);
    public function getAllMatches(): array;
    public function query();
    public function currentWeekMatch(): array;
    public function getNextWeekMatch(): ?int;
    public function currentWeek(): ?int;
    public function getMatchByWeek($week): Collection;
}
