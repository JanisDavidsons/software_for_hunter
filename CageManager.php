<?php

declare(strict_types=1);

require_once 'Cage.php';
require_once 'FileManager.php';
require_once 'FormatArray.php';

class CageManager
{
    private const CAGE_ID = 'id';
    private const OCCUPIED = 'occupied';
    private const NAME = 'name';
    private const WEIGHT = 'weight';
    private const GENDER = 'gender';
    private const CREATED_AT = 'createdAt';

    private const HEADERS = [
        self::CAGE_ID => 'Cage number',
        self::OCCUPIED => 'Occupied',
        self::NAME => 'Name',
        self::WEIGHT => 'Weight',
        self::GENDER => 'Gender',
        self::CREATED_AT => 'Created at',
    ];

    public array $cages = [];

    public function __construct()
    {
        $this->initialize();
    }

    public function validateCageId(int $cageId): bool
    {
        return !array_key_exists($cageId, $this->cages);
    }

    public function destroyCage(int $cageId): void
    {
        if ($this->validateCageId($cageId)) {
            throw new InvalidArgumentException($cageId . ' is invalid cage number');
        }

        unset($this->cages[$cageId]);
        $this->save();
    }

    public function createNewCage(?Animal $animal = null)
    {
        /** @var Cage $lastCage */
        $lastCage = end($this->cages);
        $cageId = $lastCage ? $lastCage->getId() + 1 : 1;

        $cage = new Cage($cageId, $animal);
        $this->cages[$cage->getId()] = $cage;
        $this->save();
    }

    public function putAnimalInCage(int $cageId, Animal $animal)
    {
        $this->cages[$cageId]->occupyCage($animal);
        $this->save();
    }

    public function removeAnimalFromCage(int $cageId)
    {
        /** @var Cage $cage */
        $cage = $this->cages[$cageId];

        if ($this->validateCageId($cageId)) {
            throw new InvalidArgumentException('This cage doesnt`t exists!');
        }

        if ($cage->isEmpty()) {
            throw new InvalidArgumentException('This cage is already empty!');
        }

        $cage->removeAnimal();
        $this->save();
    }

    public function getEmptyCages(): string
    {
        $cages = array_filter($this->cages, function (Cage $cage) {
            return $cage->isEmpty();
        });

        $table = new Console_Table();
        $table->setHeaders(self::HEADERS);
        foreach ($cages as $cage) {
            /** @var Cage $cage */
            $table->addRow($cage->toArray());
        }
        return $table->getTable();
    }

    public function getFullCages(): string
    {
        $table = new Console_Table();
        $table->setHeaders(self::HEADERS);

        $cages = array_filter($this->cages, function (Cage $cage) {
            return ! $cage->isEmpty();
        });

        foreach ($cages as $cage) {
            /** @var Cage $cage */
            $table->addRow($cage->toArray());
        }

        return $table->getTable();
    }

    public function getDataTable(): string
    {
        $table = new Console_Table();
        $table->setHeaders(self::HEADERS);
        $cages = FileManager::getData();

        foreach ($cages as $cage) {
            $table->addRow($cage);
        }

        return $table->getTable();
    }

    private function initialize(): void
    {
        $cagesData = FileManager::getData();

        foreach ($cagesData as $cageData) {
            $cage = new Cage(
                (int) $cageData[self::CAGE_ID],
                null,
                $cageData[self::CREATED_AT]
            );

            if (!empty($cageData[self::OCCUPIED])) {
                $animal = new Animal(
                    $cageData[self::NAME],
                    (float)$cageData[self::WEIGHT],
                    $cageData[self::GENDER]
                );
                $cage->occupyCage($animal);
            }

            $this->cages[(int)$cageData[self::CAGE_ID]] = $cage;
        }
    }

    private function save(): void
    {
        FileManager::save($this->cages, array_keys(self::HEADERS));
    }
}