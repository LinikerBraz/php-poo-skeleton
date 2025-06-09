<?php

namespace Hogwarts\Models\Module2;

use Hogwarts\Models\Base\Entity;
use Hogwarts\Models\Enums\HouseType;

class House extends Entity
{
    private HouseType $type;
    private array $students = [];
    private int $points = 0;
    private ?string $headOfHouse = null;

    public function __construct(HouseType $type)
    {
        parent::__construct();
        $this->type = $type;
    }

    public function getType(): HouseType
    {
        return $this->type;
    }

    public function getStudents(): array
    {
        return $this->students;
    }

    public function addStudent(int $studentId): void
    {
        if (!in_array($studentId, $this->students)) {
            $this->students[] = $studentId;
            $this->updateTimestamp();
        }
    }

    public function removeStudent(int $studentId): void
    {
        $key = array_search($studentId, $this->students);
        if ($key !== false) {
            unset($this->students[$key]);
            $this->students = array_values($this->students);
            $this->updateTimestamp();
        }
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function addPoints(int $points): void
    {
        $this->points += $points;
        $this->updateTimestamp();
    }

    public function removePoints(int $points): void
    {
        $this->points = max(0, $this->points - $points);
        $this->updateTimestamp();
    }

    public function getHeadOfHouse(): ?string
    {
        return $this->headOfHouse;
    }

    public function setHeadOfHouse(string $headOfHouse): void
    {
        $this->headOfHouse = $headOfHouse;
        $this->updateTimestamp();
    }

    public function getStudentCount(): int
    {
        return count($this->students);
    }
}
