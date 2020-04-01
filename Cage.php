<?php
declare(strict_types=1);
require_once 'Animal.php';

class CageIsOccupied extends LogicException
{
}

class Cage
{
    private int $cageNr = 0;
    private bool $isEmpty = true;
    private ?AnimalInterface $animalInCage;
    private string $createdAt = '';

    public function __construct(int $cageNr, ?string $createdAt=null, ?AnimalInterface $animalInCage = null)
    {
        $this->cageNr = $cageNr;

        if (!is_null($createdAt)) {
            $this->createdAt = $createdAt;
        } else {
            $this->createdAt = date("Y.m.d h:i:sA");
        }

        if ($animalInCage !== null) {
            $this->occupyCage($animalInCage);
        }
    }

    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    public function occupyCage(AnimalInterface $animal)
    {
        if (!$this->isEmpty) {
            throw new CageIsOccupied('This cage is already occupied with ' . $this->animalInCage->getName());
        }
        $this->animalInCage = $animal;
        $this->isEmpty = false;
    }

    public function getCageNr(): int
    {
        return $this->cageNr;
    }

    public function getAnimal(): AnimalInterface
    {
        return $this->animalInCage;
    }

    public function removeAnimal(): void
    {
        $this->animalInCage = null;
        $this->isEmpty = true;
    }

    public function getTimeCreatedAt(): string
    {
        return $this->createdAt;
    }
}