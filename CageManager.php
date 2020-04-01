<?php
declare(strict_types=1);
require_once 'Cage.php';
require_once 'FileManager.php';

class FormatArray
{
    static function format(array $toFormat): array
    {
        return array_map(function ($value) {
            return $value === "" ? "-" : $value;
        }, $toFormat);
    }
}

class CageIsEmpty extends LogicException
{
}

class InvalidCageNumber extends InvalidArgumentException
{
}

class CageManager
{
    private const HEADER = [
        'cageNr' => 'Cage number'
        , 'occupied' => 'Is occupied'
        , 'name' => 'Name'
        , 'weight' => 'Weight'
        , 'gender' => 'Gender'
        , 'createdAt' => 'Cage created'];

    public array $existingCages = [];

    public function __construct()
    {
        $this->initializeFromFile();
    }

    public function validateCageNr($cageNr): bool
    {
        if ((array_key_exists($cageNr, $this->existingCages)) === true) {
            return false;
        }
        return true;
    }

    public function destroyCage(int $cageNr): void
    {
        if ($this->validateCageNr($cageNr)) {
            throw new InvalidCageNumber($cageNr . ' is invalid cage number');
        }

        unset($this->existingCages[$cageNr]);
        FileManager::overWriteData($this->existingCages, self::HEADER);
    }

    public function createNewCage(?AnimalInterface $animal = null)
    {
        $newCageNr = $this->generateCageNr();
        if (!is_null($animal)) {
            $newCage = new Cage($newCageNr, null, $animal);
            $this->existingCages[$newCage->getCageNr()] = $newCage;
            FileManager::addNewRow($newCageNr, $newCage->getTimeCreatedAt(), $animal);
        } else {
            $newCage = new Cage($newCageNr);
            $this->existingCages[$newCage->getCageNr()] = $newCage;
            FileManager::addNewRow($newCageNr, $newCage->getTimeCreatedAt());
        }
    }

    public function putAnimalInCage(int $emptyCageNr, AnimalInterface $animal)
    {
        $this->existingCages[$emptyCageNr]->occupyCage($animal);
        FileManager::overWriteData($this->existingCages, self::HEADER);
    }

    public function removeAnimalFromCage(int $cageNr)
    {
        if ($this->validateCageNr($cageNr)) {
            throw new InvalidCageNumber('This cage doesnt`t exists1');
        }
        if ($this->existingCages[$cageNr]->isEmpty()) {
            throw new CageIsEmpty('This cage is already empty!');
        }

        $this->existingCages[$cageNr]->removeAnimal();
        FileManager::overWriteData($this->existingCages, self::HEADER);
    }

    private function generateCageNr(): int
    {
        $cageNr = rand(1, 100);
        if (!$this->validateCageNr($cageNr)) {
            $this->generateCageNr();
        }
        return $cageNr;
    }

    public function getEmptyCages(): string
    {
        $emptyCages = [];
        foreach ($this->existingCages as $cageNr => $cage) {
            if ($cage->isEmpty()) {
                $emptyCages[] = $cageNr;
            }
        }
        $emptyCageTable = new Console_Table();
        $emptyCageTable->setHeaders(['Empty Cage Number']);
        foreach ($emptyCages as $cage) {
            $emptyCageTable->addRow([$cage]);
        }
        return $emptyCageTable->getTable();
    }

    public function getFullCages(): string
    {
        $fullCageTable = new Console_Table();
        $fullCageTable->setHeaders(self::HEADER);
        $fullCages = [];

        foreach ($this->existingCages as $cageNr => $cage) {
            if (!$cage->isEmpty()) {
                $fullCages[] = $this->existingCages[$cageNr];
            }
        }
        foreach ($fullCages as $cage) {
            $animalProperties = $cage->getAnimal()->getAllAnimalProperties();
            $row = [$cage->getCageNr(), $cage->isEmpty() ? '-' : 'occupied'];
            foreach ($animalProperties as $animalProperty) {
                $row[] = $animalProperty;
            }
            $row[] = $cage->getTimeCreatedAt();
            $fullCageTable->addRow($row);
        }
        return $fullCageTable->getTable();
    }

    public function getDataTable(): string
    {
        $table = new Console_Table();
        $table->setHeaders(self::HEADER);
        $data = FileManager::getData();

        while (!empty($data)) {
            $table->addRow(FormatArray::format($data[0]));
            array_shift($data);
        }
        return $table->getTable();
    }

    // Im building cage and animal objects from csv file
    private function initializeFromFile(): void
    {
        $data = FileManager::getData();
        foreach ($data as $cage) {
            if (!empty($cage[self::HEADER['occupied']])) {
                $this->existingCages[(int)$cage[self::HEADER['cageNr']]] =
                    new Cage((int)$cage[self::HEADER['cageNr']], $cage[self::HEADER['createdAt']]
                        , new Animal($cage[self::HEADER['name']]
                            , (float)$cage[self::HEADER['weight']]
                            , $cage[self::HEADER['gender']]));
            } else {
                $this->existingCages[(int)$cage[self::HEADER['cageNr']]] =
                    new Cage((int)$cage[self::HEADER['cageNr']]
                        , $cage[self::HEADER['createdAt']]);
            }
        }
    }
}