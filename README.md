# Container

Lazy and naive container for the dependency injection.
Require PHP >=7.0.

[![Build Status](https://travis-ci.org/MpStyle/container.svg?branch=master)](https://travis-ci.org/MpStyle/container)

## Installation

Simply add a dependency on _mpstyle/container_ to your project's _composer.json_ file if you use Composer to manage the dependencies of your project. Here is a minimal example of a composer.json file that just defines a development-time dependency on MpStyle Container:

```json
{
    "require-dev": {
        "mpstyle/container": "1.*.*"
    }
}
```

or using console:

```
composer require "mpstyle/container=1.*.*"
```

## Usages

Simple usage of the container:

```php

interface Foo extends Injectable {}

class Dummy implements Injectable {}

class Bar implements Foo {
    public $dummy;

    public function __construct(Dummy $d){ $this->dummy = $d; }
}

$container = new Container();

// add an instance:
$container->addInstance(Foo::class, new Bar());

// or add a definition:
$container->addInstance(Foo::class, Bar::class);

// retrieve an object:
$foo =  $container->getInstance(Foo::class);

// $foo is an instance of Bar, and $dummy property of Bar is initialized as an instance of Dummy.

```

### Closure

It's possible to pass a Closure to the container which contains the logic to instantiate an object: 

```php
UniqueContainer::get()->addClosure( Foo::class, function ( Dummy $d ): Foo
{
    return new Bar( $d );
} );

/* @var $serviceB ServiceB */
$foo = UniqueContainer::get()->getInstance( Foo::class );
```

### INI File

It's possible to create a container using a PHP file collecting definitions:

_definitions.ini_:
```
mpstyle\container\dummy\Foo = mpstyle\container\dummy\Bar
```

In your PHP code:
```php
$path = 'definitions.ini';
$container = Container::fromIni($path);
$foo = $container->getInstance(Foo::class);

// $foo is an instance of Bar.
```

Closure or object are not supported using INI file.

### PHP File

It's possible to create a container using a PHP file collecting configurations: 

_definitions.php_:
```php
<?php

return [
    Foo::class => Bar::class
];
```

In your PHP code:
```php
$path = 'definitions.php';
$container = Container::fromPHP($path);

$this->assertTrue($container->existsKey(Foo::class));

$foo = $container->getInstance(Foo::class);
```

### Singleton instance

Using the wrapper of singleton instance:

```php

interface Foo extends Injectable {}

class Dummy implements Injectable {}

class Bar implements Foo {
    public $dummy;

    public function __construct(Dummy $d){ $this->dummy = $d; }
}

// add an instance:
UniqueContainer::get()->addInstance( Foo::class, new Bar(new Dummy()) );

// or add a definition:
UniqueContainer::get()->addDefinition( Foo::class, Bar::class );

// retrieve an object:
$foo =  UniqueContainer::get()->getInstance(Foo::class);

// $foo is an instance of Bar, and $dummy property of Bar is initialized as an instance of Dummy.
```

## Version

- 1.5.1: Removed unused classes.
- 1.5.0: Improved performance and tests.
- 1.4.0: Added support to load definitions from INI file and to load a container from PHP configuration file.
- 1.3.1: Little fixes.
- 1.3.0: Improved performance and stability, deprecated _Container#get(string $key)_ method, use _Container#getInstance(string $key)_ instead.
- 1.2.0: Added _Closure_ support to the container.
- 1.1.0
- 1.0.0