<?php
declare(strict_types=1);
class InvalidAnimalGender extends InvalidArgumentException{}
class InvalidAnimalName extends InvalidArgumentException{}
class InvalidAnimalWeight extends InvalidArgumentException{}

interface AnimalInterface
{
    public function getGender(): string;

    public function getWeight(): float;

    public function getName(): string;
}

class Animal implements AnimalInterface
{
    public const GENDER = [
        'male',
        'female'
    ];

    private string $name;
    private float $weight;
    private string $gender;

    public function __construct(string $name, float $weight, string $gender)
    {
        if (strlen($name) < 3 | preg_match('/\d/', $name)) {
            throw new InvalidAnimalName($name.' is invalid animal name');
        }
        if ($weight < 0 || $weight > 150) {
            throw new InvalidAnimalWeight('Please check your animal wight ' . $weight . ' is not right.');
        }

        $this->name = $name;
        $this->weight = $weight;
        $this->setGender($gender);
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    private function setGender(string $gender): void
    {
        if (in_array($gender, self::GENDER)) {
            $this->gender = $gender;
            return;
        }
        throw new InvalidAnimalGender('Unknown gender supplied!');
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getName(): string
    {
        return $this->name;
    }
}