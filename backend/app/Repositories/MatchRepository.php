<?php
namespace App\Repositories;

use App\Models\MatchModel;
use Illuminate\Database\Eloquent\Collection;

class MatchRepository implements MatchRepositoryInterface {
    protected $model;

    public function __construct()
    {
        $this->model = new MatchModel();
    }

    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function getByCriteria(array $criteria, $relations = [])
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

    public function all() {
        return $this->model->all();
    }
    public function find($id) {
        return $this->model->find($id);
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function getAllMatches(): array
    {
        return $this->model->all()->map(function($m) {
            return [
                'week'   => $m->week,
                'team_1' => $m->team1_id,
                'team_2' => $m->team2_id
            ];
        })->toArray();
    }

    public function query() {
        return $this->model->query();
    }

    /**
     * Get matches for the current week that have been simulated
     * @return array<MatchModel>
     */
    public function currentWeekMatch(): array {
        $maxWeek = $this->model
            ->where(MatchModel::SIMULATED, true)
            ->max(MatchModel::WEEK);

        if (!$maxWeek) {
            return [];
        }

        return $this->getByCriteria(
            [
                MatchModel::SIMULATED => true,
                MatchModel::WEEK => $maxWeek,
            ],
            ['team1', 'team2']
        )->toArray();
    }

    /**
     * Get the next week number that has not been simulated
     * @return int|null
     */
    public function getNextWeekMatch(): ?int {
        $criteria = [MatchModel::SIMULATED => false];

        return $this->getByCriteria($criteria)
            ->sortBy(MatchModel::WEEK)
            ->first()
            ->{MatchModel::WEEK} ?? null;
    }

    /**
     * Get the current week number (the highest week number that has been simulated)
     * @return int|null
     */
    public function currentWeek(): ?int {
        $maxWeek = MatchModel::query()
            ->where(MatchModel::SIMULATED, true)
            ->max(MatchModel::WEEK);

        return $maxWeek
            ? $this->getByCriteria([
                MatchModel::SIMULATED => true,
                MatchModel::WEEK => $maxWeek,
            ])->value(MatchModel::WEEK)
            : null;
    }

    /**
     * Get matches by week that are not yet simulated
     * @return Collection<MatchModel>
     */
    public function getMatchByWeek($week): Collection {
        return $this->getByCriteria([
            MatchModel::WEEK => $week,
            MatchModel::SIMULATED => false,
        ],
        ['team1', 'team2']);
    }
}
