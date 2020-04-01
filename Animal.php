<?php

declare(strict_types=1);

class Animal
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE
    ];

    private string $name;
    private float $weight;
    private string $gender;

    public function __construct(string $name, float $weight, string $gender)
    {
        $this->setName($name);
        $this->setWeight($weight);
        $this->setGender($gender);
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAllAnimalProperties(): array
    {
        return get_object_vars($this);
    }

    private function setName(string $name): void
    {
        if (strlen($name) < 3 | preg_match('/\d/', $name)) {
            throw new InvalidArgumentException($name . ' is invalid animal name');
        }

        $this->name = $name;
    }

    private function setWeight(float $weight): void
    {
        if ($weight < 0 || $weight > 150) {
            throw new InvalidArgumentException('Please check your animal wight ' . $weight . ' is not right.');
        }

        $this->weight = $weight;
    }

    private function setGender(string $gender): void
    {
        if (! in_array($gender, self::GENDERS)) {
            throw new InvalidArgumentException('Unknown gender supplied!');
        }

        $this->gender = $gender;
    }
}