<?php

namespace mpstyle\container;

use Exception;

class NotInjectableException extends Exception
{
    public function __construct(string $className)
    {
        parent::__construct(sprintf("%s class can not be instantiated by the container: does it extend InjectableObjectInterface? is the %s a base class of the required object?", $className, $className));
    }
}