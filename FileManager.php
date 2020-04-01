<?php
declare(strict_types=1);
define("CAGES", "cages.csv");

class FileManager
{
    public static function getData(): array
    {
        $dataWithHeader = array_map('str_getcsv', file(CAGES));
        array_shift($dataWithHeader);
        return $dataWithHeader;
    }

    public static function addNewRow(int $cageNr,string $createdAt, ?AnimalInterface $animal = null)
    {
        $fp = fopen(CAGES, 'a');
        fputcsv($fp, [$cageNr
            , !is_null($animal) ? 'occupied' : null
            , !is_null($animal) ? $animal->getName() : null
            , !is_null($animal) ? $animal->getWeight() : null
            , !is_null($animal) ? $animal->getGender() : null
            , $createdAt
        ]);
        fclose($fp);
    }

    public static function overWriteData(array $data,array $header)
    {
        $fp = fopen(CAGES, 'w');
        fputcsv($fp, $header);
        foreach ($data as $element) {
            fputcsv($fp, [
                $element->getCageNr()
                , !$element->isEmpty() ? 'occupied' : null
                , !$element->isEmpty() ? $element->getAnimal()->getName() : null
                , !$element->isEmpty() ? $element->getAnimal()->getWeight() : null
                , !$element->isEmpty() ? $element->getAnimal()->getGender() : null
                , $element->getTimeCreatedAt()
            ]);
        }
        fclose($fp);
    }
}