<?php
require_once 'libraries/Table.php';
require_once 'CageManager.php';
require_once 'Animal.php';
require_once 'Cage.php';

class FormatUserInput
{
    static public function format(array $userInput): array
    {
        return array_map(function ($element) {
            return trim($element);
        }, $userInput);
    }
}

class InvalidOption extends InvalidArgumentException
{
}

$softWareRunning = true;
$cageManager = new CageManager();

while ($softWareRunning) {

    echo PHP_EOL . "\e[0;32m Welcome to hunting! \e[0m" . PHP_EOL . PHP_EOL;
    echo "\e[0;32m Options available: \e[0m" . PHP_EOL;
    echo "\e[0;32m To show all hunted animals:\e[0;34m type 1 \e[0m" . PHP_EOL;
    echo "\e[0;32m To add new animal in new cage:\e[0;34m type 2 \e[0m" . PHP_EOL;
    echo "\e[0;32m To create empty cage :\e[0;34m type 3 \e[0m" . PHP_EOL;
    echo "\e[0;32m To show all empty cages :\e[0;34m type 4 \e[0m" . PHP_EOL;
    echo "\e[0;32m To add new animal existing new cage :\e[0;34m type 5 \e[0m" . PHP_EOL;
    echo "\e[0;32m To remove animal from cage :\e[0;34m type 6 \e[0m" . PHP_EOL;
    echo "\e[0;32m To destroy cage :\e[0;34m type 7 \e[0m" . PHP_EOL;
    echo "\e[0;32m To get cages with animals in it :\e[0;34m type 8 \e[0m" . PHP_EOL;
    echo "\e[0;32m To stop software:\e[0;34m type 9 \e[0m" . PHP_EOL;

    $hunterCommand = readline('Please enter your command: ');
    echo PHP_EOL;

    switch ($hunterCommand) {
        case 1:
            echo $cageManager->getDataTable() . PHP_EOL;
            break;
        case 2:
            $name = trim(readline('Please enter animal name: '));
            $weight = (float)readline('Please animal weight: ');
            $gender = readline('Please animal gender: ');

            try {
                $cageManager->createNewCage(new Animal($name, $weight, $gender));
                echo $cageManager->getDataTable() . PHP_EOL;
                echo "\e[1;33m" . 'Your ' . $name . ' is added to cage!' . "\e[0m" . PHP_EOL;
            } catch (InvalidAnimalName | InvalidAnimalGender $exception) {
                echo "\e[0;31m" . $exception->getMessage() . "\e[0m" . PHP_EOL;
            }
            break;
        case 3:
            $cageManager->createNewCage();
            echo $cageManager->getDataTable() . PHP_EOL;
            break;
        case 4:
            echo $cageManager->getEmptyCages() . PHP_EOL;
            break;
        case 5:
            echo $cageManager->getEmptyCages() . PHP_EOL;
            $cageNr = trim(readline('Please choose existing empty cage from table above: '));
            $animalData = askUserAnimalData();
            try {
                $cageManager->putAnimalInCage($cageNr
                    , new Animal($animalData['name'], (float)$animalData['weight'], $animalData['gender']));
                echo $cageManager->getDataTable() . PHP_EOL;
            } catch (CageIsOccupied $exception) {
                echo "\e[0;31m" . $exception->getMessage() . "\e[0m" . PHP_EOL;
            }
            break;
        case 6:
            $cageNr = trim(readline('To remove animal, please choose cage number: '));
            try {
                $cageManager->removeAnimalFromCage($cageNr);
                echo $cageManager->getDataTable() . PHP_EOL;
            } catch (InvalidCageNumber | CageIsEmpty $exception) {
                echo "\e[0;31m" . $exception->getMessage() . "\e[0m" . PHP_EOL;
            }
            break;
        case 7:
            $cageNr = trim(readline('Please enter cage number to destroy: '));

            try {
                $cageManager->destroyCage($cageNr);
                echo $cageManager->getDataTable() . PHP_EOL;
            } catch (CageIsOccupied $exception) {
                echo "\e[0;31m" . $exception->getMessage() . "\e[0m" . PHP_EOL;
            }
            break;
        case 8:
            echo $cageManager->getFullCages() . PHP_EOL;
            break;
        case 9:
            $softWareRunning = false;
            break;
        default:
            echo new InvalidOption("\e[0;31m" . 'Invalid option, please try again!' . "\e[0m");
    }
}

function askUserAnimalData(): array
{
    $animalData = [];
    $name = trim(readline('Please enter animal name: '));
    $weight = readline('Please animal weight: ');
    $gender = readline('Please animal gender: ');

    $animalData['name'] = $name;
    $animalData['weight'] = $weight;
    $animalData['gender'] = $gender;
    return FormatUserInput::format($animalData);
}