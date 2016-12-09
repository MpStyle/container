<?php

namespace mpstyle\container\dummy;

use mpstyle\container\Injectable;

class ServiceB implements Injectable, BaseService
{
    /**
     * @var ServiceA
     */
    private $serviceA;

    /**
     * @var ServiceC
     */
    private $serviceC;

    /**
     * ServiceB constructor.
     * @param ServiceA $serviceA
     * @param ServiceC $serviceC
     */
    public function __construct(ServiceA $serviceA, ServiceC $serviceC)
    {
        $this->serviceA = $serviceA;
        $this->serviceC = $serviceC;
    }

    /**
     * @return ServiceA
     */
    public function getServiceA(): ServiceA
    {
        return $this->serviceA;
    }

    /**
     * @return ServiceC
     */
    public function getServiceC(): ServiceC
    {
        return $this->serviceC;
    }


}