# Container

Lazy and naive container for the dependency injection.
Require PHP >=7.0.

## Installation

Simply add a dependency on _mpstyle/container_ to your project's _composer.json_ file if you use Composer to manage the dependencies of your project. Here is a minimal example of a composer.json file that just defines a development-time dependency on MpStyle Container:

```json
{
    "require-dev": {
        "mpstyle/container": "1.0.*"
    }
}
```

or using console:

```
composer require "mpstyle/container=1.0.*"
```

## Usages

Using the wrapper of singleton instance:

```php

interface Foo {}

class Dummy {}

class Bar implements Foo {
    public $dummy;

    public function __construct(Dummy $d){ $this->dummy = $d; }
}

// add an instance:
UniqueContainer::get()->addInstance(Foo::class, new Bar());

// or add a definition:
UniqueContainer::get()->addInstance(Foo::class, Bar::class);

// retrieve an object:
$foo =  UniqueContainer::get()->get(Foo::class);

// $foo is an instance of Bar, and $dummy property of Bar is initialized as an instance of Dummy.
```
