<?php

declare(strict_types=1);

require_once 'Animal.php';

class Cage
{
    private int $id;
    private ?Animal $animal = null;
    private ?string $createdAt = null;

    public function __construct(int $id, ?Animal $animal = null, ?string $createdAt = null)
    {
        $this->id = $id;

        if ($animal !== null) {
            $this->occupyCage($animal);
        }

        $this->createdAt = $this->createdAt ?? date("Y.m.d h:i:sA");
    }

    public function isEmpty(): bool
    {
        return $this->animal === null;
    }

    public function occupyCage(Animal $animal)
    {
        if (! $this->isEmpty()) {
            throw new LogicException('This cage is already occupied with ' . $this->animal->getName());
        }

        $this->animal = $animal;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAnimal(): Animal
    {
        return $this->animal;
    }

    public function removeAnimal(): void
    {
        $this->animal = null;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        $animal = $this->animal;

        return [
            'id' => $this->getId(),
            'occupied' => ! $this->isEmpty(),
            'name' => $animal ? $this->getAnimal()->getName() : null,
            'weight' => $animal ? $animal->getWeight() : null,
            'gender' => $animal ? $animal->getGender() : null,
            'createdAt' => $this->getCreatedAt()
        ];
    }
}