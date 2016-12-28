<?php

namespace mpstyle\container;

use mpstyle\container\dummy\BaseService;
use mpstyle\container\dummy\Foo;

class ContainerFromIniTest extends \PHPUnit_Framework_TestCase
{
    public function test_fromIni_01()
    {
        $path = __DIR__ . '/../../../resources/definitions.ini';
        $container = Container::fromIni($path);

        $this->assertTrue($container->existsKey(BaseService::class));
        $this->assertTrue($container->existsKey(Foo::class));

        $baseService = $container->getInstance(BaseService::class);
        $foo = $container->getInstance(Foo::class);

        $this->assertTrue($baseService instanceof BaseService);
        $this->assertTrue($foo instanceof Foo);
    }

    public function test_fromIni_02()
    {
        $this->expectException(NotInjectableException::class);
        $path = __DIR__ . '/../../../resources/fallable_definitions.ini';
        Container::fromIni($path);
    }

    protected function setUp()
    {
        parent::setUp();
    }


    protected function tearDown()
    {
        parent::tearDown();
        UniqueContainer::get()->clear();
    }
}