<?php

namespace Hogwarts\Models\Module5;

use Hogwarts\Models\Base\Person;
use Hogwarts\Models\Enums\HouseType;

class Professor extends Person
{
    private array $subjects = [];
    private ?HouseType $house = null;
    private string $specialization;
    private \DateTime $hireDate;
    private bool $active = true;

    public function __construct(string $name, string $email, \DateTime $birthDate, string $specialization, string $address = '')
    {
        parent::__construct($name, $email, $birthDate, $address);
        $this->specialization = $specialization;
        $this->hireDate = new \DateTime();
    }

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    public function addSubject(int $subjectId): void
    {
        if (!in_array($subjectId, $this->subjects)) {
            $this->subjects[] = $subjectId;
            $this->updateTimestamp();
        }
    }

    public function removeSubject(int $subjectId): void
    {
        $key = array_search($subjectId, $this->subjects);
        if ($key !== false) {
            unset($this->subjects[$key]);
            $this->subjects = array_values($this->subjects);
            $this->updateTimestamp();
        }
    }

    public function getHouse(): ?HouseType
    {
        return $this->house;
    }

    public function setHouse(HouseType $house): void
    {
        $this->house = $house;
        $this->updateTimestamp();
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): void
    {
        $this->specialization = $specialization;
        $this->updateTimestamp();
    }

    public function getHireDate(): \DateTime
    {
        return $this->hireDate;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
        $this->updateTimestamp();
    }

    public function deactivate(): void
    {
        $this->active = false;
        $this->updateTimestamp();
    }

    public function getYearsOfService(): int
    {
        return $this->hireDate->diff(new \DateTime())->y;
    }
}
