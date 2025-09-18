<?php

namespace App\DTO;

class StandingDTO
{
    public $team_id;
    public $team_name;
    public $played;
    public $wins;
    public $draws;
    public $losses;
    public $goals_for;
    public $goals_against;
    public $goal_difference;
    public $points;

    public function __construct($team_id, $team_name, $played, $wins, $draws, $losses, $goals_for, $goals_against, $goal_difference, $points)
    {
        $this->team_id = $team_id;
        $this->team_name = $team_name;
        $this->played = $played;
        $this->wins = $wins;
        $this->draws = $draws;
        $this->losses = $losses;
        $this->goals_for = $goals_for;
        $this->goals_against = $goals_against;
        $this->goal_difference = $goal_difference;
        $this->points = $points;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data['team_id'] ?? null,
            $data['team_name'] ?? null,
            $data['played'] ?? 0,
            $data['wins'] ?? 0,
            $data['draws'] ?? 0,
            $data['losses'] ?? 0,
            $data['goals_for'] ?? 0,
            $data['goals_against'] ?? 0,
            $data['goal_difference'] ?? 0,
            $data['points'] ?? 0
        );
    }

    public function toArray()
    {
        return [
            'team_id' => $this->team_id,
            'team_name' => $this->team_name,
            'played' => $this->played,
            'wins' => $this->wins,
            'draws' => $this->draws,
            'losses' => $this->losses,
            'goals_for' => $this->goals_for,
            'goals_against' => $this->goals_against,
            'goal_difference' => $this->goal_difference,
            'points' => $this->points,
        ];
    }
}
