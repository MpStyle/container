<?php

namespace mpstyle\container;

use mpstyle\container\dummy\BaseService;
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
        $this->expectException(NotInjectableException::class);
        UniqueContainer::get()->get(ServiceD::class);
    }

    public function test_get()
    {
        $serviceA = UniqueContainer::get()->get(ServiceA::class);
        $this->assertTrue($serviceA instanceof ServiceA);
    }

    public function test_fails_addDefinition()
    {
        $this->expectException(ClassDoesNotExistException::class);
        UniqueContainer::get()->addDefinition(BaseService::class, "ciao");
    }

    public function test_addDefinition()
    {
        UniqueContainer::get()->addDefinition(BaseService::class, ServiceB::class);
        /* @var $serviceB ServiceB */
        $serviceB = UniqueContainer::get()->get(BaseService::class);
        $this->assertTrue($serviceB instanceof ServiceB);
        $this->assertTrue($serviceB->getServiceA() instanceof ServiceA);
        $this->assertTrue($serviceB->getServiceC() instanceof ServiceC);
    }

    public function test_addInstance()
    {
        UniqueContainer::get()->addInstance(ServiceC::class, new ServiceC());
        $serviceC = UniqueContainer::get()->get(ServiceC::class);
        $this->assertTrue($serviceC instanceof ServiceC);
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
