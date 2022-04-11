<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson\Type;

class Obj implements TypeInterface
{
    private string $className;

    public function __construct(string $className, private bool $isNullable)
    {
        $this->className = $this->underscoreToCamelCase($className, true);
    }

    public function getPhpName(): string
    {
        return ($this->isNullable ? '?' : '') . $this->className;
    }

    private function underscoreToCamelCase($string, $capitalizeFirstCharacter = false): string
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }
}