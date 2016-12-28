<?php

namespace mpstyle\container;

use mpstyle\container\dummy\BaseService;
use mpstyle\container\dummy\Dummy;
use mpstyle\container\dummy\Foo;
use mpstyle\container\dummy\ServiceC;

class ContainerFromPHPTest extends \PHPUnit_Framework_TestCase
{
    public function test_fromIni_01()
    {
        $path = __DIR__ . '/../../../resources/definitions.php';
        $container = Container::fromPHP( $path );

        $this->assertTrue( $container->existsKey( BaseService::class ) );
        $this->assertTrue( $container->existsKey( Foo::class ) );

        $baseService = $container->getInstance( BaseService::class );
        $foo = $container->getInstance( Foo::class );
        $serviceC = $container->getInstance( ServiceC::class );
        $dummy = $container->getInstance( Dummy::class );

        $this->assertTrue( $baseService instanceof BaseService );
        $this->assertTrue( $foo instanceof Foo );
        $this->assertTrue( $serviceC instanceof ServiceC );
        $this->assertTrue( $dummy instanceof Dummy );

    }

    public function test_fromIni_02()
    {
        $this->expectException( NotInjectableException::class );
        $path = __DIR__ . '/../../../resources/fallable_definitions.php';
        Container::fromPHP( $path );
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