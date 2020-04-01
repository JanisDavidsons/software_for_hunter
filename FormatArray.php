<?php

declare(strict_types=1);

class FormatArray
{
    public static function format(array $toFormat): array
    {
        return array_map(function ($value) {
            return $value === "" ? "-" : $value;
        }, $toFormat);
    }
}
