<?php
namespace App\DTO;

class PredictionsDTO
{
    public $name;
    public $percentage;

    public function __construct(string $name, int $percentage)
    {
        $this->name = $name;
        $this->percentage = $percentage;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            (int)($data['percentage'] ?? 0)
        );
    }

    /**
     * @param array $teams Array of ['name' => string, 'percentage' => int]
     * @return PredictionsDTO[]
     */
    public static function collection(array $teams): array
    {
        return array_map(function($team) {
            return self::fromArray($team);
        }, $teams);
    }
}
