<?php

namespace mpstyle\container;

use Exception;

class NotInjectableException extends Exception
{
    public function __construct(string $className)
    {
        parent::__construct(sprintf("%s class can not be instantiated by the container.", $className));
    }
}