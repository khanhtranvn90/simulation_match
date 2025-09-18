<?php
namespace App\DTO;

class WeekMatchDTO
{
    public $id;
    public $home;
    public $home_score;
    public $away;
    public $away_score;
    public $week;
    public $finished;

    public function __construct($id, $home, $home_score, $away, $away_score, $week, $finished = 0)
    {
        $this->id = $id;
        $this->home = $home;
        $this->home_score = $home_score;
        $this->away = $away;
        $this->away_score = $away_score;
        $this->week = $week;
        $this->finished = $finished;
    }

    public static function fromArray(array $data)
    {
        return new self(
            $data['id'] ?? null,
            $data['home'] ?? null,
            $data['home_score'] ?? null,
            $data['away'] ?? null,
            $data['away_score'] ?? null,
            $data['week'] ?? null,
            $data['week'] === (int)config('app.league_duration') ? 1 : 0
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'home' => $this->home,
            'home_score' => $this->homeScore,
            'away' => $this->away,
            'away_score' => $this->awayScore,
            'week' => $this->week,
            'finished' => $this->finished
        ];
    }
}
