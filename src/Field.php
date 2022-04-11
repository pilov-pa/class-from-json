<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson;

use PilovPa\ClassFromJson\Type\TypeInterface;

class Field
{
    private string $name = '';
    private TypeInterface $type;
    private array $annotations = [];

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    public function setType(TypeInterface $type): Field
    {
        $this->type = $type;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $this->underscoreToCamelCase($name);
        return $this;
    }

    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    public function setAnnotations(array $annotations): self
    {
        $this->annotations = $annotations;
        return $this;
    }

    public function addAnnotation($name): self
    {
        $this->annotations[] = $name;
        return $this;
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