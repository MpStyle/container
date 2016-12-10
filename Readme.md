# Container

Lazy and naive container for the dependency injection.
Require PHP >=7.0.

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

Simple usage of container:

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
$foo =  $container->get(Foo::class);

// $foo is an instance of Bar, and $dummy property of Bar is initialized as an instance of Dummy.

```

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
$foo =  UniqueContainer::get()->get(Foo::class);

// $foo is an instance of Bar, and $dummy property of Bar is initialized as an instance of Dummy.
```

### Constructor

The constructor of the _container_ class has a single parameter: _$settings_.
This is an array of configuration.
The only one supported value is _trigger_error (default: false).
If it is setted to _true_ will be triggered error messages when:
- it's required an object not present in the container (NOTICE)
- it's added a definition already present in the container (WARNING)
- it's added an instance already present in the container (WARNING)