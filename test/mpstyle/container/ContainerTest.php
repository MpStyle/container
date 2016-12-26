<?php

namespace mpstyle\container;

use mpstyle\container\dummy\Bar;
use mpstyle\container\dummy\BaseService;
use mpstyle\container\dummy\Dummy;
use mpstyle\container\dummy\Foo;
use mpstyle\container\dummy\ServiceA;
use mpstyle\container\dummy\ServiceB;
use mpstyle\container\dummy\ServiceC;
use mpstyle\container\dummy\ServiceD;
use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function test_fail_get()
    {
        $this->expectException( NotInjectableException::class );
        UniqueContainer::get()->get( ServiceD::class );
    }

    public function test_get()
    {
        $serviceA = UniqueContainer::get()->get( ServiceA::class );
        $this->assertTrue( $serviceA instanceof ServiceA );
    }

    public function test_fails_addDefinition()
    {
        $this->expectException( NotInjectableException::class );
        UniqueContainer::get()->addDefinition( BaseService::class, "ciao" );
    }

    public function test_addClosure()
    {
        UniqueContainer::get()->addClosure( BaseService::class, function ( ServiceA $serviceA, ServiceC $serviceC )
        {
            return new ServiceB( $serviceA, $serviceC );
        } );

        /* @var $serviceB ServiceB */
        $serviceB = UniqueContainer::get()->get( BaseService::class );
        $this->assertTrue( $serviceB instanceof ServiceB );
        $this->assertTrue( $serviceB->getServiceA() instanceof ServiceA );
        $this->assertTrue( $serviceB->getServiceC() instanceof ServiceC );
    }

    public function test_addDefinition()
    {
        UniqueContainer::get()->addDefinition( BaseService::class, ServiceB::class );
        /* @var $serviceB ServiceB */
        $serviceB = UniqueContainer::get()->get( BaseService::class );
        $this->assertTrue( $serviceB instanceof ServiceB );
        $this->assertTrue( $serviceB->getServiceA() instanceof ServiceA );
        $this->assertTrue( $serviceB->getServiceC() instanceof ServiceC );
    }

    public function test_addInstance()
    {
        UniqueContainer::get()->addInstance( ServiceC::class, new ServiceC() );
        $serviceC = UniqueContainer::get()->get( ServiceC::class );
        $this->assertTrue( $serviceC instanceof ServiceC );
    }

    public function test_readmeUsage_01()
    {
        UniqueContainer::get()->addInstance( Foo::class, new Bar(new Dummy()) );

        $foo = UniqueContainer::get()->get( Foo::class );

        $this->assertTrue( $foo instanceof Foo );
        $this->assertTrue( $foo instanceof Bar );

        UniqueContainer::get()->clear();

        UniqueContainer::get()->addDefinition( Foo::class, Bar::class );

        $foo = UniqueContainer::get()->get( Foo::class );

        $this->assertTrue( $foo instanceof Foo );
        $this->assertTrue( $foo instanceof Bar );
    }

    public function test_readmeUsage_02()
    {
        $container = new Container();

        $container->addInstance( Foo::class, new Bar(new Dummy()) );

        $foo = $container->get( Foo::class );

        $this->assertTrue( $foo instanceof Foo );
        $this->assertTrue( $foo instanceof Bar );

        $container->clear();

        $container->addDefinition( Foo::class, Bar::class );

        $foo = $container->get( Foo::class );

        $this->assertTrue( $foo instanceof Foo );
        $this->assertTrue( $foo instanceof Bar );
    }

    protected function setUp()
    {
        parent::setUp();
        PHPUnit_Framework_Error_Warning::$enabled = false;
        PHPUnit_Framework_Error_Notice::$enabled = false;
    }


    protected function tearDown()
    {
        parent::tearDown();
        UniqueContainer::get()->clear();
    }


}
