# class-from-json [![Unit tests](https://github.com/pilov-pa/class-from-json/actions/workflows/php.yml/badge.svg)](https://github.com/pilov-pa/class-from-json/actions/workflows/php.yml)

This package uses for generating class code based on json.

## Basic usage

```php
$generator = new \PilovPa\ClassFromJson\ClassMapGenerator();
$renderer = new \PilovPa\ClassFromJson\ClassRenderer('8.1');
$classMap = $generator->generate('Some\Root\Ns', $someJson);

foreach ($classMap as $cls) {
    $classTemplate = $renderer->renderClass($cls);
    file_put_contents(__DIR__ . '/Some/Root/Ns/' . $cls->getName() . '.php', $classTemplate);
}
```

## Example

You can generate class file by any json. For example

```json
{
    "booleanField": true,
    "stringField": "some string",
    "objectField": {
        "intField": 123,
        "nullField": null
    }
}
```

will translate to a couple of files:

```php
<?php

declare(strict_types=1);

namespace Some\Root\Ns;

class RootClass
{
    private bool $booleanField;
    private string $stringField;
    private ObjectField $objectField;

    public function getBooleanField(): bool
    {
        return $this->booleanField;
    }

    public function setBooleanField(bool $booleanField): RootClass
    {
        $this->booleanField = $booleanField;
        return $this;
    }

    public function getStringField(): string
    {
        return $this->stringField;
    }

    public function setStringField(string $stringField): RootClass
    {
        $this->stringField = $stringField;
        return $this;
    }

    public function getObjectField(): ObjectField
    {
        return $this->objectField;
    }

    public function setObjectField(ObjectField $objectField): RootClass
    {
        $this->objectField = $objectField;
        return $this;
    }
}
```

and

```php
<?php

declare(strict_types=1);

namespace Some\Root\Ns;

class ObjectField
{
    private int $intField;
    private mixed $nullField;

    public function getIntField(): int
    {
        return $this->intField;
    }

    public function setIntField(int $intField): RootClass
    {
        $this->intField = $intField;
        return $this;
    }

    public function getNullField(): mixed
    {
        return $this->nullField;
    }

    public function setNullField(mixed $nullField): RootClass
    {
        $this->nullField = $nullField;
        return $this;
    }
}
```
## Generated properties
Each property in generated classes will have `private` modifier, getter ang fluent setter.
### Property names

Class's property names generates from a json field name. under_scored names will transform to camelCase.

### Property types

Class's property types generates from a json field value type. Next json types are supported:

| json value                     | php type          | annotation        |
|--------------------------------|-------------------|-------------------|
| "string"                       | string            |
| 123                            | int               |
| 0.25                           | float             |
| true/false                     | bool              |
| null                           | mixed             |
| []                             | array             |
| {"field1": 1}                  | SomeChildrenClass |
| [1,2,3]                        | array             | @var int[]        |
| ["a", "b"]                     | array             | @var string[]     |
| [{"field1": 1}, {"field1": 2}] | array             | @var SomeChildrenClass[] |

Two json fields in different places in json structure who have same names and different structures will transform to two different classes.
