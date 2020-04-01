<?php

declare(strict_types=1);

define("CAGES", "cages.csv");

class FileManager
{
    public static function getData(): array
    {
        $rawData = array_map('str_getcsv', file(CAGES));
        $header = array_shift($rawData);      //dropping header part
        $result = [];
        foreach ($rawData as $cage){
            $result[] = array_combine($header,$cage);
        }
        return $result;
    }

    public static function save(array $data, array $header)
    {
        $fp = fopen(CAGES, 'w');
        fputcsv($fp, $header);
        foreach ($data as $cage) {
            /** @var Cage $cage */
            fputcsv($fp, [
                $cage->getId(),
                ! $cage->isEmpty() ? 'occupied' : null,
                ! $cage->isEmpty() ? $cage->getAnimal()->getName() : null,
                ! $cage->isEmpty() ? $cage->getAnimal()->getWeight() : null,
                ! $cage->isEmpty() ? $cage->getAnimal()->getGender() : null,
                $cage->getCreatedAt()
            ]);
        }
        fclose($fp);
    }
}