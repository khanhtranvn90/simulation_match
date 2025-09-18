<?php
namespace App\DTO;

class ChampionDTO
{
    public $name;
    public $pts;
    public $win;
    public $loss;
    public $draw;
    public $played;
    public $finished;

    public function __construct(string $name, int $pts, int $win, int $loss, int $draw, int $played, bool $finished)
    {
        $this->name = $name;
        $this->pts = $pts;
        $this->win = $win;
        $this->loss = $loss;
        $this->draw = $draw;
        $this->played = $played;
        $this->finished = $finished;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            (int)($data['pts'] ?? 0),
            (int)($data['win'] ?? 0),
            (int)($data['loss'] ?? 0),
            (int)($data['draw'] ?? 0),
            (int)($data['played'] ?? 0),
            (bool)($data['finished'] ?? false)
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'pts' => $this->pts,
            'win' => $this->win,
            'loss' => $this->loss,
            'draw' => $this->draw,
            'played' => $this->played,
            'finished' => $this->finished
        ];
    }
}
