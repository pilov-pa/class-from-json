<?php

declare(strict_types=1);

namespace PilovPa\ClassFromJson;

class ClassRenderer
{
    private const TEMPLATES_DIR = __DIR__ . '/templates';

    public function __construct(private string $phpVersion = '8.1')
    {}

    private function getTemplate(string $templateName): string
    {
        return file_get_contents(self::TEMPLATES_DIR . '/' . $this->phpVersion . '/' . $templateName);
    }

    public function renderClass(Cls $class): string
    {
        $fileTemplate = $this->getTemplate('fileTemplate.php.tpl');
        $fileTemplate = str_replace('{{namespace}}', $class->getNamespace(), $fileTemplate);
        $classResult = $this->clsToClassTemplate($class);
        return str_replace('{{class}}', $classResult, $fileTemplate);
    }

    private function clsToClassTemplate(Cls $cls): string
    {
        $classTemplate = $this->getTemplate('classTemplate.php.tpl');
        $getterTemplate = $this->getTemplate('getterTemplate.php.tpl');
        $setterTemplate = $this->getTemplate('setterTemplate.php.tpl');

        $fieldsString = '';

        foreach ($cls->getFields() as $field) {
            if (count($annotations = $field->getAnnotations())) {
                $fieldsString .= "    /**\n";
                foreach ($annotations as $annotation) {
                    $fieldsString .= "     * $annotation\n";
                }
                $fieldsString .= "    **/\n";
            }
            $fieldsString .= sprintf("    private %s $%s;\n", $field->getType()->getPhpName(), $field->getName());
        }

        foreach ($cls->getFields() as $field) {
            $getter = $getterTemplate;
            $getter = str_replace(
                ['{{nameUpper}}', '{{type}}', '{{name}}'],
                [ucfirst($field->getName()), $field->getType()->getPhpName(), $field->getName()],
                $getter
            );

            $fieldsString .= $getter;

            $setter = $setterTemplate;
            $setter = str_replace(
                ['{{nameUpper}}', '{{type}}', '{{name}}', '{{className}}'],
                [ucfirst($field->getName()), $field->getType()->getPhpName(), $field->getName(), $cls->getName()],
                $setter
            );

            $fieldsString .= $setter;
        }

        $classTemplate = str_replace(
            ['{{className}}', '{{fields}}'],
            [$cls->getName(), $fieldsString],
            $classTemplate
        );

        return $classTemplate;
    }
}