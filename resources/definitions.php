<?php

use mpstyle\container\dummy\BaseService;
use mpstyle\container\dummy\Dummy;
use mpstyle\container\dummy\ServiceB;
use mpstyle\container\dummy\Foo;
use mpstyle\container\dummy\Bar;
use mpstyle\container\dummy\ServiceC;

return [
    BaseService::class => ServiceB::class,
    Foo::class => Bar::class,
    ServiceC::class => new ServiceC(),
    Dummy::class => function ():Dummy
    {
        return new Dummy();
    }
];