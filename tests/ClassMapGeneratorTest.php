<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PilovPa\ClassFromJson\ClassMapGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClassMapGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $expectedMainClassFields = [
            'testString' => ['string', []],
            'testInt' => ['int', []],
            'testFloat' => ['float', []],
            'testObject' => ['TestObject', []],
            'testNull' => ['mixed', []],
            'testBoolTrue' => ['bool', []],
            'testBoolFalse' => ['bool', []],
            'testArrayOfObjects' => ['array', ['@var TestArrayOfObject[]']],
            'testArrayOfString' => ['array', ['@var string[]']],
            'testEmptyArray' => ['array', []],
            'testSameNameDifferentContent' => ['TestSameNameDifferentContent', []],
            'testSameNameSameContent' => ['TestSameNameSameContent', []],
            'testThreeLevels' => ['TestThreeLevel', []],
        ];
        $expectedThreeLevelsClassFields = [
            'level2' => ['Level2', []],
        ];
        $expectedLevel2ClassFields = [
            'level3' => ['Level3', []],
        ];
        $expectedClassesList = [
            "TestObject", "TestArrayOfObject", "TestSameNameDifferentContentTestObject", "TestSameNameDifferentContent",
            "TestSameNameSameContent", "Level3", "Level2", "TestThreeLevel", "RootClass",
        ];
        $json = file_get_contents(__DIR__ . '/data/testGenerator.json');
        $generator = new ClassMapGenerator();
        $classMap = $generator->generate('Test\Namesp', $json);

        $actualClassesList = array_keys($classMap);
        sort($actualClassesList);
        sort($expectedClassesList);
        $this->assertSame($expectedClassesList, $actualClassesList);

        $mainClass = $classMap['RootClass'];
        $fields = $mainClass->getFields();
        $this->assertCount(13, $fields);

        foreach ($fields as $field) {
            $this->assertEquals($expectedMainClassFields[$field->getName()][0], $field->getType()->getPhpName());
            $this->assertEquals($expectedMainClassFields[$field->getName()][1], $field->getAnnotations());
        }

        $testThreeLevelClass = $classMap['TestThreeLevel'];
        $testThreeLevelClassFields = $testThreeLevelClass->getFields();

        foreach ($testThreeLevelClassFields as $field) {
            $this->assertEquals($expectedThreeLevelsClassFields[$field->getName()][0], $field->getType()->getPhpName());
            $this->assertEquals($expectedThreeLevelsClassFields[$field->getName()][1], $field->getAnnotations());
        }

        $testLevel2Class = $classMap['Level2'];
        $testLevel2ClassFields = $testLevel2Class->getFields();

        foreach ($testLevel2ClassFields as $field) {
            $this->assertEquals($expectedLevel2ClassFields[$field->getName()][0], $field->getType()->getPhpName());
            $this->assertEquals($expectedLevel2ClassFields[$field->getName()][1], $field->getAnnotations());
        }
    }
}