<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson\Type;

class SimpleType implements TypeInterface
{
    protected const NAME = '';

    public function __construct(private bool $nullable)
    {}

    public function getPhpName(): string
    {
        return ($this->nullable ? '?' : '') . static::NAME;
    }
}