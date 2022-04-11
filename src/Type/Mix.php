<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson\Type;

class Mix implements TypeInterface
{
    public function getPhpName(): string
    {
        return 'mixed';
    }
}