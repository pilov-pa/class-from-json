<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson;

use PilovPa\ClassFromJson\Type\Arr;
use PilovPa\ClassFromJson\Type\Boolean;
use PilovPa\ClassFromJson\Type\Double;
use PilovPa\ClassFromJson\Type\Integer;
use PilovPa\ClassFromJson\Type\Mix;
use PilovPa\ClassFromJson\Type\Obj;
use PilovPa\ClassFromJson\Type\Str;
use PilovPa\ClassFromJson\Type\TypeInterface;

class ClassMapGenerator
{
    /** @var Cls[] */
    private array $classMap;

    private string $namespace;

    /**
     * @return Cls[]
     */
    public function generate(string $namespace, string $json): array
    {
        $this->classMap = [];
        $this->namespace = $namespace;

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $class = $this->dataToCls('Cls', $data);
        $this->classMap[$class->getName()] = $class;

        return $this->classMap;
    }

    private function addToClassMap(Cls $class, ?Cls $outerClass = null): Cls
    {
        $className = $class->getName();

        if (array_key_exists($className, $this->classMap)) {
            if ($this->isClssIdentical($class, $this->classMap[$className])) {
                return $class;
            }

            $className = $outerClass?->getName() . $className;
        }

        $class->setName($className);
        $this->classMap[$className] = $class;

        return $class;
    }

    private function dataToCls(string $className, array $data): Cls
    {
        $cls = new Cls();
        $cls->setNamespace($this->namespace);
        $cls->setName($className);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_int(key($value))) {
                    $cls->addField($this->processArray($key, $value, $cls));
                } else {
                    $cls->addField($this->processObject($key, $value, $cls));
                }
            } else {
                $cls->addField($this->processScalar($key, $value));
            }
        }

        return $cls;
    }

    private function processArray(string $key, array $value, Cls $outerClass): Field
    {
        $field = new Field();
        $field
            ->setName($key)
            ->setType(new Arr(false));

        if (count($value)) {
            if (is_array($value[0])) {
                $class = $this->dataToCls($key, $value[0]);
                $this->addToClassMap($class, $outerClass);
                $field->addAnnotation("@var {$class->getName()}[]");
            } else {
                if ($value[0] === null) {
                    $type = new Mix();
                } else {
                    $phpType = gettype($value[0]);
                    $typesMap = [
                        'integer' => Integer::class,
                        'boolean' => Boolean::class,
                        'double' => Double::class,
                        'string' => Str::class,
                    ];
                    /** @var TypeInterface $type */
                    $type = new ($typesMap[$phpType])(false);
                }
                $field->addAnnotation("@var {$type->getPhpName()}[]");
            }
        }

        return $field;
    }

    private function processObject(string $key, array $value, Cls $outerClass): Field
    {
        $class = $this->dataToCls($key, $value);
        $class = $this->addToClassMap($class, $outerClass);
        $field = new Field();
        $field
            ->setName($key)
            ->setType(new Obj($class->getName(), false));

        return $field;
    }

    private function processScalar(string $key, int|bool|float|string|null $value): Field
    {
        if ($value === null) {
            $type = new Mix();
        } else {
            $phpType = gettype($value);
            $typesMap = [
                'integer' => Integer::class,
                'boolean' => Boolean::class,
                'double' => Double::class,
                'string' => Str::class,
            ];
            /** @var TypeInterface $type */
            $type = new ($typesMap[$phpType])(false);
        }
        $field = new Field();
        $field
            ->setName($key)
            ->setType($type);

        return $field;
    }

    private function isClssIdentical(Cls $clsFirst, Cls $clsSecond): bool
    {
        $fieldsFirst = array_combine(
            array_map(static fn(Field $field) => $field->getName(), $clsFirst->getFields()),
            $clsFirst->getFields()
        );

        $fieldsSecond = array_combine(
            array_map(static fn(Field $field) => $field->getName(), $clsSecond->getFields()),
            $clsSecond->getFields()
        );

        if (count($fieldsFirst) !== count($fieldsSecond)) {
            return false;
        }

        ksort($fieldsFirst);
        ksort($fieldsSecond);

        for ($i = 0, $iMax = count($fieldsFirst); $i < $iMax; $i++) {
            $field1 = $fieldsFirst[$i];
            $field2 = $fieldsSecond[$i];

            if ($field1->getName() !== $field2->getName()) {
                return false;
            }

            if ($field1->getType()->getPhpName() !== $field2->getType()->getPhpName()) {
                return false;
            }
        }

        return true;
    }
}