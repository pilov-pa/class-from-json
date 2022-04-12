# class-from-json
Needs to generate classes from json

## Basic usage

```php
$generator = new \PilovPa\ClassFromJson\ClassMapGenerator();
$renderer = new \PilovPa\ClassFromJson\ClassRenderer('8.1');
$classMap = $generator->generate('Some\Root\Namespace', $someJson);

foreach ($classMap as $cls) {
    $classTemplate = $renderer->renderClass($cls);
    file_put_contents(__DIR__ . '/Some/Root/Namespace/' . $cls->getName() . '.php', $classTemplate);
}
```