<?php

namespace Hogwarts\Models\Module5;

use Hogwarts\Models\Base\Person;

class Staff extends Person
{
    private string $position;
    private string $department;
    private \DateTime $hireDate;
    private bool $active = true;
    private float $salary;

    public function __construct(string $name, string $email, \DateTime $birthDate, string $position, string $department, float $salary, string $address = '')
    {
        parent::__construct($name, $email, $birthDate, $address);
        $this->position = $position;
        $this->department = $department;
        $this->salary = $salary;
        $this->hireDate = new \DateTime();
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
        $this->updateTimestamp();
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function setDepartment(string $department): void
    {
        $this->department = $department;
        $this->updateTimestamp();
    }

    public function getHireDate(): \DateTime
    {
        return $this->hireDate;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): void
    {
        $this->salary = $salary;
        $this->updateTimestamp();
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
