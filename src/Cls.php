<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson;

class Cls
{
    private string $name = '';
    private string $namespace = '';

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): Cls
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @var Field[]
     */
    private array $fields = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Cls
    {
        $this->name = $this->underscoreToCamelCase($name, true);

        if (str_ends_with($this->name, 'ses')) {
            $this->name = substr($this->name, 0, -2);
        } elseif (!str_ends_with($this->name, 'ss') && str_ends_with($this->name, 's')) {
            $this->name = substr($this->name, 0, -1);
        }

        $keywords = array('parent', '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');
        if (in_array(strtolower($this->name), $keywords)) {
            $this->name .= '1';
        }

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
     * @return $this
     */
    public function setFields(array $fields): Cls
    {
        $this->fields = $fields;
        return $this;
    }

    public function addField(Field $field): Cls
    {
        $this->fields[] = $field;
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