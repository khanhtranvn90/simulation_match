<?php
namespace App\DTO;

class MatchDTO
{
    public $team1_id;
    public $team2_id;
    public $week;
    public $team1_score;
    public $team2_score;
    public $simulated;

    public function __construct($team1_id, $team2_id, $week, $team1_score, $team2_score, $simulated)
    {
        $this->team1_id = $team1_id;
        $this->team2_id = $team2_id;
        $this->week = $week;
        $this->team1_score = $team1_score;
        $this->team2_score = $team2_score;
        $this->simulated = $simulated;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data[\App\Models\MatchModel::TEAM_1] ?? null,
            $data[\App\Models\MatchModel::TEAM_2] ?? null,
            $data[\App\Models\MatchModel::WEEK] ?? null,
            $data[\App\Models\MatchModel::TEAM_1_SCORE] ?? null,
            $data[\App\Models\MatchModel::TEAM_2_SCORE] ?? null,
            $data[\App\Models\MatchModel::SIMULATED] ?? false
        );
    }

    public function toArray()
    {
        return [
            \App\Models\MatchModel::TEAM_1 => $this->team1_id,
            \App\Models\MatchModel::TEAM_2 => $this->team2_id,
            \App\Models\MatchModel::WEEK => $this->week,
            \App\Models\MatchModel::TEAM_1_SCORE => $this->team1_score,
            \App\Models\MatchModel::TEAM_2_SCORE => $this->team2_score,
            \App\Models\MatchModel::SIMULATED => $this->simulated
        ];
    }
}
