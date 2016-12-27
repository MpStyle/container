<?php

use mpstyle\container\dummy\BaseService;
use mpstyle\container\dummy\ServiceB;
use mpstyle\container\dummy\Foo;
use mpstyle\container\dummy\Bar;

return [
    BaseService::class => ServiceB::class,
    Foo::class => Bar::class,
    BaseService::class => "ciao"
];