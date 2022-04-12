<?php

declare(strict_types=1);

namespace Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use PilovPa\ClassFromJson\ClassMapGenerator;
use PilovPa\ClassFromJson\ClassRenderer;
use PilovPa\ClassFromJson\Cls;
use PilovPa\ClassFromJson\Field;
use PilovPa\ClassFromJson\Type\Arr;
use PilovPa\ClassFromJson\Type\Obj;
use PilovPa\ClassFromJson\Type\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClassRendererTest extends TestCase
{
    private function prepareCls(): Cls
    {
        $namespace = 'Test\Namesp';

        $cls = new Cls();
        $cls
            ->setNamespace($namespace)
            ->setName('TestClass')
            ->addField((new Field())->setName('testString')->setType(new Str(false)))
            ->addField((new Field())->setName('testIntArray')->setType(new Arr(false))->setAnnotations(['@var int[]']))
            ->addField((new Field())->setName('testObject')->setType(new Obj('TestObject', false)))
        ;

        return $cls;
    }

    public function testRender(): void
    {
        $renderer = new ClassRenderer();

        $renderedClass = $renderer->renderClass($this->prepareCls());

        $this->assertEquals(file_get_contents(__DIR__ . '/data/testRendererExpectedClass.php.txt'), $renderedClass);
    }
}